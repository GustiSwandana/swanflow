const https = require('https');

https.get('https://swanflow.space/login', (res) => {
    let data = '';
    res.on('data', chunk => data += chunk);
    res.on('end', () => {
        console.log('Status:', res.statusCode);
        const actions = data.match(/action=["'][^"']+["']/g);
        console.log('Found form actions:', actions);
        const scripts = data.match(/src=["'][^"']+["']/g);
        console.log('Sample script/css URLs:', scripts ? scripts.slice(0, 5) : null);
        const hasHttp = data.includes('http://swanflow.space');
        console.log('Contains http://swanflow.space ?', hasHttp);
    });
}).on('error', (e) => {
    console.error('Error:', e.message);
});
