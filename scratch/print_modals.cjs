const fs = require('fs');
const html = fs.readFileSync('scratch/dash_live.html', 'utf8');

const editStart = html.indexOf('id="edit-transaction-modal"');
console.log('--- EDIT TRANSACTION MODAL (start:', editStart, ') ---');
if (editStart !== -1) {
    console.log(html.slice(editStart, editStart + 7000));
}

const deleteStart = html.indexOf('id="delete-transaction-confirm-modal"');
console.log('--- DELETE CONFIRM MODAL (start:', deleteStart, ') ---');
if (deleteStart !== -1) {
    console.log(html.slice(deleteStart, deleteStart + 4000));
}
