const fs = require('fs');

async function testLoginAndTransactions() {
    console.log('1. Fetching login page for CSRF token & cookies...');
    const loginPageRes = await fetch('https://swanflow.space/login');
    const loginHtml = await loginPageRes.text();
    
    // Extract CSRF token
    const tokenMatch = loginHtml.match(/name="_token"\s+value="([^"]+)"/);
    if (!tokenMatch) {
        console.error('Could not find CSRF token on login page');
        return;
    }
    const csrfToken = tokenMatch[1];
    
    // Extract cookies
    const rawCookies = loginPageRes.headers.getSetCookie 
        ? loginPageRes.headers.getSetCookie() 
        : [loginPageRes.headers.get('set-cookie')];
    const cookieHeader = rawCookies.map(c => c.split(';')[0]).join('; ');
    console.log('Got CSRF token and cookies:', cookieHeader);

    console.log('2. Attempting login...');
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

    console.log('Login response status:', loginRes.status);
    console.log('Login response location:', loginRes.headers.get('location'));
    
    const loginCookies = loginRes.headers.getSetCookie 
        ? loginRes.headers.getSetCookie() 
        : [loginRes.headers.get('set-cookie')];
    const sessionCookieHeader = loginCookies.map(c => c.split(';')[0]).join('; ') || cookieHeader;

    console.log('3. Fetching /transactions with session cookie...');
    const txRes = await fetch('https://swanflow.space/transactions', {
        headers: {
            'Cookie': sessionCookieHeader
        }
    });

    console.log('/transactions response status:', txRes.status, 'redirected:', txRes.redirected, 'URL:', txRes.url);
    const txHtml = await txRes.text();
    fs.writeFileSync('scratch/transactions_live.html', txHtml);
    console.log('Saved live transactions HTML to scratch/transactions_live.html (size:', txHtml.length, 'bytes)');

    // Look for openDeleteTransactionModal or delete buttons
    const deleteMatches = txHtml.match(/openDeleteTransactionModal\([^)]+\)/g);
    console.log('Found openDeleteTransactionModal calls in HTML:', deleteMatches ? deleteMatches.length : 0);
    if (deleteMatches && deleteMatches.length > 0) {
        console.log('Sample delete call:', deleteMatches[0]);
    }
}

testLoginAndTransactions().catch(console.error);
