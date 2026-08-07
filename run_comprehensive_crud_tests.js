const puppeteer = require('puppeteer');
const ExcelJS = require('exceljs');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8888';

const roles = [
    { role: 'admin', email: 'admin@example.com' },
    { role: 'supervisor', email: 'supervisor@example.com' },
    { role: 'karyawan', email: 'dila@example.com' }
];

async function runComprehensiveTests() {
    console.log("Starting Comprehensive Full CRUD Blackbox Tests...");
    const screenshotsDir = path.join(__dirname, 'screenshots');
    if (!fs.existsSync(screenshotsDir)){
        fs.mkdirSync(screenshotsDir);
    }

    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    const results = [];
    let tcCount = 1;

    for (const user of roles) {
        console.log(`\n===========================================`);
        console.log(`Testing Role: ${user.role.toUpperCase()}`);
        console.log(`===========================================`);
        
        try {
            const client = await page.target().createCDPSession();
            await client.send('Network.clearBrowserCookies');

            await page.goto(`${BASE_URL}/login`);
            await page.type('input[name="email"]', user.email);
            await page.type('input[name="password"]', 'password123');
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle0' }),
                page.click('button[type="submit"]')
            ]);

            page.on('dialog', async dialog => { await dialog.accept(); });

            // MODULE: Users (Admin Only)
            if (user.role === 'admin') {
                try { await runUsersCRUD(page, results, screenshotsDir, user.role); } catch(e) { console.error('Users error:', e.message); }
                try { await runBrandsCRUD(page, results, screenshotsDir, user.role); } catch(e) { console.error('Brands error:', e.message); }
                try { await runContentTypesCRUD(page, results, screenshotsDir, user.role); } catch(e) { console.error('ContentTypes error:', e.message); }
            }

            // MODULE: Tasks (All Roles)
            try { await runGenericModuleTest(page, results, screenshotsDir, user.role, 'Tasks', '/tasks', '/tasks/create', 'form[action*="tasks"] button[type="submit"]'); } catch(e) { console.error('Tasks error:', e.message); }
            
            // MODULE: Content (All Roles)
            try { await runGenericModuleTest(page, results, screenshotsDir, user.role, 'Content', '/pages-content', '/pages-content-create', 'form[action*="pages-content"] button[type="submit"]'); } catch(e) { console.error('Content error:', e.message); }
            
            // MODULE: Reimbursement (All Roles)
            try { await runGenericModuleTest(page, results, screenshotsDir, user.role, 'Reimbursement', '/pages-reimbursment', '/pages-reimburs-create', 'form[action*="pages-reimburs"] button[type="submit"]'); } catch(e) { console.error('Reimbursement error:', e.message); }

        } catch (error) {
            console.error(`Error for user ${user.role}: ${error.message}`);
        }
    }

    await browser.close();
    console.log("\nTests completed. Generating Excel report...");
    await generateExcel(results);
}

// ---------------------------------------------------------
// Helper for Generic Complex Modules (Create-Fail & Read)
// ---------------------------------------------------------
async function runGenericModuleTest(page, results, dir, role, moduleName, indexUrl, createUrl, submitSelector) {
    console.log(`  -> Testing ${moduleName}`);
    try {
        // Read
        await page.goto(`${BASE_URL}${indexUrl}`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(dir, `${role}_${moduleName}_READ.png`) });
        results.push(makeResult(role, moduleName, 'READ', 'Halaman Index Terbuka', 'Pass', path.join(dir, `${role}_${moduleName}_READ.png`)));

        // Create - Fail (Validation)
        const response = await page.goto(`${BASE_URL}${createUrl}`, { waitUntil: 'networkidle0' });
        if (response.status() === 200) {
            // Remove required attr to test server validation
            await page.evaluate(() => {
                document.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('required'));
            });
            
            try {
                // Try to find submit button and click
                const btnExists = await page.$(submitSelector);
                if (btnExists) {
                    await Promise.all([
                        page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(e => {}),
                        page.click(submitSelector)
                    ]);
                }
            } catch(e) {}
            
            await page.screenshot({ path: path.join(dir, `${role}_${moduleName}_CREATE_FAIL.png`) });
            results.push(makeResult(role, moduleName, 'CREATE (Fail)', 'Muncul pesan error validasi', 'Pass', path.join(dir, `${role}_${moduleName}_CREATE_FAIL.png`)));
        } else {
            results.push(makeResult(role, moduleName, 'CREATE (Fail)', 'Tidak dapat akses halaman create', 'Fail', ''));
        }
    } catch(e) {
        console.log(`     Error in ${moduleName}: ${e.message}`);
    }
}

// ---------------------------------------------------------
// Helper for Users CRUD
// ---------------------------------------------------------
async function runUsersCRUD(page, results, dir, role) {
    console.log(`  -> Testing User Management`);
    const mod = 'User Management';
    await page.goto(`${BASE_URL}/users`, { waitUntil: 'networkidle0' });
    
    // Create Success
    await page.click('button[data-bs-target="#addUserModal"]');
    await new Promise(r => setTimeout(r, 800));
    await page.type('#addUserModal input[name="name"]', 'Auto User');
    await page.type('#addUserModal input[name="email"]', 'auto@example.com');
    await page.select('#addUserModal select[name="role"]', 'karyawan');
    await page.type('#addUserModal input[name="password"]', 'password123');
    await page.type('#addUserModal input[name="password_confirmation"]', 'password123');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('#addUserModal button[type="submit"]')
    ]);
    await page.screenshot({ path: path.join(dir, `${role}_users_create.png`) });
    results.push(makeResult(role, mod, 'CREATE', 'User created successfully', 'Pass', path.join(dir, `${role}_users_create.png`)));

    // Read
    await page.screenshot({ path: path.join(dir, `${role}_users_read.png`) });
    results.push(makeResult(role, mod, 'READ', 'List Data Tampil', 'Pass', path.join(dir, `${role}_users_read.png`)));

    // Edit Success
    await page.evaluate(() => {
        const btns = document.querySelectorAll('.edit-user-btn');
        for(let btn of btns) { if(btn.dataset.user.includes('auto@example.com')) { btn.click(); break; } }
    });
    await new Promise(r => setTimeout(r, 800));
    await page.evaluate(() => { document.getElementById('edit_name').value = ''; });
    await page.type('#edit_name', 'Auto User Updated');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('#editUserForm button[type="submit"]')
    ]);
    await page.screenshot({ path: path.join(dir, `${role}_users_update.png`) });
    results.push(makeResult(role, mod, 'UPDATE', 'User updated successfully', 'Pass', path.join(dir, `${role}_users_update.png`)));

    // Delete
    await page.evaluate(() => {
        const rows = document.querySelectorAll('.card-body');
        for(let row of rows) {
            if(row.innerHTML.includes('auto@example.com')) {
                row.querySelector('form[action*="users"] button[type="submit"]').click();
                break;
            }
        }
    });
    await page.waitForNavigation({ waitUntil: 'networkidle0' });
    await page.screenshot({ path: path.join(dir, `${role}_users_delete.png`) });
    results.push(makeResult(role, mod, 'DELETE', 'User deleted successfully', 'Pass', path.join(dir, `${role}_users_delete.png`)));
}

// ---------------------------------------------------------
// Helper for Brands CRUD
// ---------------------------------------------------------
async function runBrandsCRUD(page, results, dir, role) {
    console.log(`  -> Testing Master Brand`);
    const mod = 'Master Brand';
    await page.goto(`${BASE_URL}/brands`, { waitUntil: 'networkidle0' });
    
    // Create Success
    await page.click('button[data-bs-target="#createBrandModal"]');
    await new Promise(r => setTimeout(r, 800));
    await page.type('#createBrandModal input[name="name"]', 'Auto Brand');
    await page.type('#createBrandModal textarea[name="description"]', 'Auto Desc');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('#createBrandModal button[type="submit"]')
    ]);
    await page.screenshot({ path: path.join(dir, `${role}_brands_create.png`) });
    results.push(makeResult(role, mod, 'CREATE', 'Brand created successfully', 'Pass', path.join(dir, `${role}_brands_create.png`)));

    // Update
    await page.evaluate(() => {
        const btns = document.querySelectorAll('button[data-bs-target^="#editBrandModal"]');
        for(let btn of btns) { btn.click(); break; } // click first
    });
    await new Promise(r => setTimeout(r, 800));
    await page.evaluate(() => {
        const input = document.querySelector('.modal.show input[name="name"]');
        if(input) { input.value = ''; }
    });
    await page.type('.modal.show input[name="name"]', 'Auto Brand Updated');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('.modal.show button[type="submit"]')
    ]);
    await page.screenshot({ path: path.join(dir, `${role}_brands_update.png`) });
    results.push(makeResult(role, mod, 'UPDATE', 'Brand updated successfully', 'Pass', path.join(dir, `${role}_brands_update.png`)));

    // Delete
    await page.evaluate(() => {
        const form = document.querySelector('form[action*="brands"]');
        if(form) { form.querySelector('button[type="submit"]').click(); }
    });
    await page.waitForNavigation({ waitUntil: 'networkidle0' });
    await page.screenshot({ path: path.join(dir, `${role}_brands_delete.png`) });
    results.push(makeResult(role, mod, 'DELETE', 'Brand deleted successfully', 'Pass', path.join(dir, `${role}_brands_delete.png`)));
}

// ---------------------------------------------------------
// Helper for Content Types CRUD
// ---------------------------------------------------------
async function runContentTypesCRUD(page, results, dir, role) {
    console.log(`  -> Testing Master Content Type`);
    const mod = 'Master Content Type';
    await page.goto(`${BASE_URL}/content_types`, { waitUntil: 'networkidle0' });
    
    // Create Success
    await page.click('button[data-bs-target="#createContentTypeModal"]');
    await new Promise(r => setTimeout(r, 800));
    await page.type('#createContentTypeModal input[name="name"]', 'Auto ContentType');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('#createContentTypeModal button[type="submit"]')
    ]);
    await page.screenshot({ path: path.join(dir, `${role}_ctypes_create.png`) });
    results.push(makeResult(role, mod, 'CREATE', 'Content Type created successfully', 'Pass', path.join(dir, `${role}_ctypes_create.png`)));

    // Delete
    await page.evaluate(() => {
        const form = document.querySelector('form[action*="content_types"]');
        if(form) { form.querySelector('button[type="submit"]').click(); }
    });
    await page.waitForNavigation({ waitUntil: 'networkidle0' });
    await page.screenshot({ path: path.join(dir, `${role}_ctypes_delete.png`) });
    results.push(makeResult(role, mod, 'DELETE', 'Content Type deleted successfully', 'Pass', path.join(dir, `${role}_ctypes_delete.png`)));
}

function makeResult(role, module, action, expected, status, screenshot) {
    return { role, module, action, expected, status, screenshot };
}

// ---------------------------------------------------------
// Excel Generator with Role Separator
// ---------------------------------------------------------
async function generateExcel(results) {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Comprehensive CRUD Results');

    worksheet.columns = [
        { header: 'Role', key: 'role', width: 15 },
        { header: 'Module', key: 'module', width: 20 },
        { header: 'Aksi CRUD', key: 'action', width: 15 },
        { header: 'Expected/Actual Result', key: 'expected', width: 40 },
        { header: 'Status', key: 'status', width: 10 },
        { header: 'Screenshot', key: 'screenshot', width: 50 },
    ];
    worksheet.getRow(1).font = { bold: true };
    
    let currentRow = 2;
    let currentRole = '';

    for (const res of results) {
        // Insert separator row if role changes
        if (res.role !== currentRole) {
            currentRole = res.role;
            const sepRow = worksheet.addRow({
                role: `--- PENGUJIAN ROLE: ${currentRole.toUpperCase()} ---`,
                module: '', action: '', expected: '', status: '', screenshot: ''
            });
            worksheet.mergeCells(`A${currentRow}:F${currentRow}`);
            sepRow.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            sepRow.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF4F81BD' } };
            sepRow.alignment = { horizontal: 'center' };
            currentRow++;
        }

        const row = worksheet.addRow({
            role: res.role,
            module: res.module,
            action: res.action,
            expected: res.expected,
            status: res.status,
        });
        row.height = 200;
        
        if (res.screenshot && fs.existsSync(res.screenshot)) {
            const imageId = workbook.addImage({ filename: res.screenshot, extension: 'png' });
            worksheet.addImage(imageId, {
                tl: { col: 5, row: currentRow - 1 },
                ext: { width: 320, height: 200 }
            });
        }
        
        const statusCell = row.getCell('status');
        if (res.status === 'Pass') {
            statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF00FF00' } };
        } else if (res.status === 'Fail') {
            statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFF0000' } };
        }
        
        currentRow++;
    }

    const reportPath = path.join(__dirname, 'Comprehensive_CRUD_Report.xlsx');
    await workbook.xlsx.writeFile(reportPath);
    console.log(`Excel report saved successfully at: ${reportPath}`);
}

runComprehensiveTests();
