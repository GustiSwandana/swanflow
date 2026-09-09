const fs = require('fs');
const path = require('path');

const uploadUrl = 'https://srv1762-files.hstgr.io/rest/6f4b8632f568bd73/api/tus/public_html';
const authKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTI5OTAyNDQ0NyIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODk4MjM4NCwiaWF0IjoxNzg4OTYwNzg0fQ.IitLPabVmKtgPzkC-Pjr5cghj_YTcvGfAKD-RsWnUYc';
const restAuthKey = '1aa1852a21986e5b1eb07061ede495399aaac3c63003eb666f44f895d549914b-6f4b8632f568bd73';

async function testUploadMobileBlade() {
    const filePath = 'c:/laragon/www/swanflow/resources/views/layouts/mobile.blade.php';
    const remotePath = 'swanflow/resources/views/layouts/mobile.blade.php';
    const fileBuffer = fs.readFileSync(filePath);
    const size = fileBuffer.length;
    const targetUrl = `${uploadUrl}/${remotePath}?override=true`;

    console.log(`Testing upload of mobile.blade.php (${size} bytes) to ${targetUrl}`);

    const postRes = await fetch(targetUrl, {
        method: 'POST',
        headers: {
            'X-Auth': authKey,
            'X-Auth-Rest': restAuthKey,
            'Tus-Resumable': '1.0.0',
            'Upload-Length': size.toString(),
            'Upload-Offset': '0'
        }
    });

    console.log('POST status:', postRes.status);
    console.log('POST headers:', Object.fromEntries(postRes.headers.entries()));

    const patchRes = await fetch(targetUrl, {
        method: 'PATCH',
        headers: {
            'X-Auth': authKey,
            'X-Auth-Rest': restAuthKey,
            'Tus-Resumable': '1.0.0',
            'Upload-Offset': '0',
            'Content-Type': 'application/offset+octet-stream',
            'Content-Length': size.toString()
        },
        body: fileBuffer
    });

    console.log('PATCH status:', patchRes.status);
    console.log('PATCH headers:', Object.fromEntries(patchRes.headers.entries()));
    const patchBody = await patchRes.text();
    console.log('PATCH body:', patchBody.slice(0, 200));
}

testUploadMobileBlade().catch(console.error);
