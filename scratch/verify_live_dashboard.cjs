async function testLiveDashboard() {
    console.log('1. Logging in with gustiswandana@swanflow.com...');
    const loginPageRes = await fetch('https://swanflow.space/login');
    const loginHtml = await loginPageRes.text();
    const tokenMatch = loginHtml.match(/name="_token"\s+value="([^"]+)"/);
    const csrfToken = tokenMatch ? tokenMatch[1] : '';

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

    console.log('2. Fetching / (Beranda)...');
    const dashRes = await fetch(`https://swanflow.space/?_cb=${Date.now()}`, {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const dashHtml = await dashRes.text();

    console.log('Beranda HTTP status:', dashRes.status);
    console.log('Check 1: Contains data-transaction-row ->', dashHtml.includes('data-transaction-row'));
    console.log('Check 2: Contains trash button with openDeleteFromDataset ->', dashHtml.includes('openDeleteFromDataset'));
    console.log('Check 3: Contains inline confirmation edit-delete-confirm-box ->', dashHtml.includes('edit-delete-confirm-box'));
    console.log('Check 4: Contains delete-transaction-confirm-modal with z-index: 9999 ->', dashHtml.includes('z-index: 9999'));
    console.log('Check 5: Contains executeDeleteTransaction ->', dashHtml.includes('executeDeleteTransaction'));
    console.log('Check 6: Contains app-DfjEBG8p.css ->', dashHtml.includes('app-DfjEBG8p.css'));
    console.log('Check 7: Contains liquid-card ->', dashHtml.includes('liquid-card'));
    console.log('Check 8: Contains liquid-dock-capsule ->', dashHtml.includes('liquid-dock-capsule'));
    console.log('Check 9: Contains ios-press ->', dashHtml.includes('ios-press'));
    console.log('Check 10: Contains animate-liquid-orb-1 ->', dashHtml.includes('animate-liquid-orb-1'));

    console.log('\n3. Fetching /transactions...');
    const txRes = await fetch(`https://swanflow.space/transactions?_cb=${Date.now()}`, {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const txHtml = await txRes.text();
    console.log('Transactions HTTP status:', txRes.status);
    console.log('Check 11: Transactions has ios-segmented-track ->', txHtml.includes('ios-segmented-track'));
    console.log('Check 12: Transactions has ios-segmented-thumb ->', txHtml.includes('ios-segmented-thumb'));
    console.log('Check 13: Transactions has liquid-card ->', txHtml.includes('liquid-card'));

    // Extract transaction IDs from data-transaction-row
    const ids = [];
    const idRegex = /data-transaction-row="(\d+)"/g;
    let m;
    while ((m = idRegex.exec(dashHtml)) !== null) {
        ids.push(m[1]);
    }
    console.log('\nTransactions found on Beranda:', ids);
}

testLiveDashboard().catch(console.error);

