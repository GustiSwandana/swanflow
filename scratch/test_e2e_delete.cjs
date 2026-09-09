async function testEndToEndDelete() {
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

    console.log('2. Creating temporary test transaction on Beranda...');
    const dashRes = await fetch(`https://swanflow.space/?_cb=${Date.now()}`, {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const dashHtml = await dashRes.text();
    const dashCsrfMatch = dashHtml.match(/name="_token"\s+value="([^"]+)"/);
    const dashCsrf = dashCsrfMatch ? dashCsrfMatch[1] : csrfToken;

    // Create a 1500 IDR test transaction
    const createRes = await fetch('https://swanflow.space/transactions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cookie': sessionCookieHeader,
            'X-CSRF-TOKEN': dashCsrf,
            'Accept': 'text/html,application/xhtml+xml,application/xml'
        },
        body: new URLSearchParams({
            '_token': dashCsrf,
            'type': 'expense',
            'amount': '1500',
            'wallet_id': '1',
            'category_id': '1',
            'date': new Date().toISOString().split('T')[0],
            'description': 'Test Hapus Beranda'
        }),
        redirect: 'manual'
    });
    console.log('Create transaction status:', createRes.status);

    // Fetch Beranda to find the newly created transaction
    const dashAfterRes = await fetch(`https://swanflow.space/?_cb=${Date.now()}`, {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const dashAfterHtml = await dashAfterRes.text();
    
    // Find transaction with description "Test Hapus Beranda"
    const regex = /data-transaction-row="(\d+)"[^>]*>[\s\S]*?Test Hapus Beranda/;
    const match = dashAfterHtml.match(regex);
    if (!match) {
        console.log('Could not find created transaction row on Beranda');
        return;
    }
    const createdTxId = match[1];
    console.log(`Found test transaction with ID ${createdTxId} on Beranda!`);

    // Now simulate executeDeleteTransaction via AJAX POST with _method=DELETE
    console.log(`3. Deleting transaction ${createdTxId} using AJAX executeDeleteTransaction...`);
    const deleteRes = await fetch(`https://swanflow.space/transactions/${createdTxId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Cookie': sessionCookieHeader,
            'X-CSRF-TOKEN': dashCsrf,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            _method: 'DELETE'
        })
    });

    const deleteJson = await deleteRes.json();
    console.log('Delete API response:', deleteJson);

    // Fetch Beranda again to confirm it is gone
    const verifyRes = await fetch(`https://swanflow.space/?_cb=${Date.now()}`, {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const verifyHtml = await verifyRes.text();
    const stillPresent = verifyHtml.includes(`data-transaction-row="${createdTxId}"`);
    console.log(`Transaction ${createdTxId} still present on Beranda:`, stillPresent ? 'YES (FAIL)' : 'NO (SUCCESS - DELETED!)');
}

testEndToEndDelete().catch(console.error);
