const fs = require('fs');

async function checkDashboardLive() {
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

    console.log('Fetching https://swanflow.space/ (Beranda)...');
    const dashRes = await fetch('https://swanflow.space/', {
        headers: { 'Cookie': sessionCookieHeader }
    });
    const dashHtml = await dashRes.text();
    fs.writeFileSync('scratch/dash_live.html', dashHtml);
    console.log('Saved live dashboard HTML (size:', dashHtml.length, 'bytes)');

    console.log('Has data-transaction-row?', dashHtml.includes('data-transaction-row'));
    console.log('Has openEditFromDataset?', dashHtml.includes('openEditFromDataset'));
    console.log('Has delete-transaction-confirm-modal?', dashHtml.includes('delete-transaction-confirm-modal'));
    console.log('Has edit-transaction-modal?', dashHtml.includes('edit-transaction-modal'));
    console.log('Has executeDeleteTransaction?', dashHtml.includes('executeDeleteTransaction'));

    // Check recent transactions section in dashboard HTML
    const match = dashHtml.match(/data-transaction-row="(\d+)"/g);
    console.log('Dashboard data-transaction-row matches:', match);
}

checkDashboardLive().catch(console.error);
