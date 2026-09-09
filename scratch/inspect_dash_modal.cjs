const fs = require('fs');

const html = fs.readFileSync('scratch/dash_live.html', 'utf8');

console.log('--- DASHBOARD HTML INSPECTION ---');

// Check modal IDs
const modals = ['edit-transaction-modal', 'delete-transaction-confirm-modal', 'global-delete-transaction-form'];
modals.forEach(id => {
    console.log(`Has #${id}?`, html.includes(`id="${id}"`));
});

// Check functions in script
const funcs = [
    'openEditFromDataset',
    'openDeleteFromDataset',
    'openEditTransactionModal',
    'openDeleteTransactionModal',
    'triggerDeleteFromEditModal',
    'executeDeleteTransaction'
];
funcs.forEach(fn => {
    console.log(`Has function ${fn}?`, html.includes(`function ${fn}`));
});

// Check triggerDeleteFromEditModal button in edit-transaction-modal
const editModalSnippet = html.slice(html.indexOf('id="edit-transaction-modal"'), html.indexOf('id="edit-transaction-modal"') + 3000);
console.log('\nEdit modal has triggerDeleteFromEditModal button?', editModalSnippet.includes('triggerDeleteFromEditModal'));

// Check delete-transaction-confirm-modal snippet
const deleteModalSnippet = html.slice(html.indexOf('id="delete-transaction-confirm-modal"'), html.indexOf('id="delete-transaction-confirm-modal"') + 2500);
console.log('\nDelete modal has confirm-delete-submit-btn?', deleteModalSnippet.includes('confirm-delete-submit-btn'));
console.log('Delete modal has executeDeleteTransaction?', deleteModalSnippet.includes('executeDeleteTransaction'));

// Check transaction rows in dashboard
const txRowMatches = html.match(/<div data-transaction-row="(\d+)"[^>]*>/g);
console.log('\nTransaction rows found on dashboard:', txRowMatches ? txRowMatches.length : 0);
if (txRowMatches) {
    console.log('Sample row:', txRowMatches[0]);
}
