const fs = require('fs');

async function testDeleteTransaction() {
    console.log('1. Logging in...');
    const loginPageRes = await fetch('https://swanflow.space/login');
    const loginHtml = await loginPageRes.text();
    const tokenMatch = loginHtml.match(/name="_token"\s+value="([^"]+)"/);
    const csrfToken = tokenMatch[1];
    
    const rawCookies = loginPageRes.headers.getSetCookie 
        ? loginPageRes.headers.getSetCookie() 
        : [loginPageRes.headers.get('set-cookie')];
    const cookieHeader = rawCookies.map(c => c.split(';')[0]).join('; ');

    const loginRes = await fetch('https://swanflow.space/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cookie': cookieHeader,
            'X-CSRF-TOKEN': csrfToken
        },
        body: new URLSearchParams({
            '_token': csrfToken,
            'email': 'gustiswandana@swanflow.com',
            'password': 'password'
        }),
        redirect: 'manual'
    });

    const loginCookies = loginRes.headers.getSetCookie 
        ? loginRes.headers.getSetCookie() 
        : [loginRes.headers.get('set-cookie')];
    const sessionCookieHeader = loginCookies.map(c => c.split(';')[0]).join('; ') || cookieHeader;

    console.log('2. Fetching /transactions to find a transaction ID and CSRF token...');
    const txRes = await fetch('https://swanflow.space/transactions', {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const txHtml = await txRes.text();
    
    // Extract CSRF token from the transaction page
    const txTokenMatch = txHtml.match(/name="_token"\s+value="([^"]+)"/);
    const txCsrf = txTokenMatch ? txTokenMatch[1] : csrfToken;
    console.log('Transaction page CSRF token:', txCsrf ? txCsrf.slice(0, 15) + '...' : 'NONE');

    // Extract transaction IDs from data-transaction-row
    const ids = [];
    const idRegex = /data-transaction-row="(\d+)"/g;
    let m;
    while ((m = idRegex.exec(txHtml)) !== null) {
        ids.push(m[1]);
    }
    console.log('Found transaction IDs via data-transaction-row:', ids);

    if (ids.length === 0) {
        console.log('No transactions to test delete.');
        return;
    }

    const testId = ids[0];
    console.log(`\n3. Testing modern AJAX FETCH DELETE (exact new UI behavior) for ID ${testId}...`);
    const fetchDeleteRes = await fetch(`https://swanflow.space/transactions/${testId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Cookie': sessionCookieHeader,
            'X-CSRF-TOKEN': txCsrf,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            '_method': 'DELETE'
        })
    });

    console.log('Fetch delete status:', fetchDeleteRes.status);
    const fetchDeleteJson = await fetchDeleteRes.json();
    console.log('Fetch delete JSON response:', fetchDeleteJson);

    console.log('\n4. Fetching /transactions to verify ID is removed...');
    const afterRes = await fetch('https://swanflow.space/transactions', {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const afterHtml = await afterRes.text();
    const remainingIds = [];
    let remM;
    const remRegex = /data-transaction-row="(\d+)"/g;
    while ((remM = remRegex.exec(afterHtml)) !== null) {
        remainingIds.push(remM[1]);
    }
    console.log('Remaining IDs after delete:', remainingIds);
    console.log(`Did testId (${testId}) get deleted?`, !remainingIds.includes(testId));
}

testDeleteTransaction().catch(console.error);
