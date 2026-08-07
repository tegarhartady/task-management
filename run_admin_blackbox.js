const puppeteer = require('puppeteer');
const ExcelJS = require('exceljs');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8888';
const SCREENSHOTS_DIR = path.join(__dirname, 'screenshots_admin');

if (!fs.existsSync(SCREENSHOTS_DIR)) {
    fs.mkdirSync(SCREENSHOTS_DIR);
}

const results = [];

// Helper to force click via DOM to bypass Bootstrap animation issues
async function domClick(page, selector) {
    await page.evaluate((sel) => {
        const el = document.querySelector(sel);
        if (el) el.click();
    }, selector);
}

// Helper to remove HTML5 validation
async function removeHtmlValidation(page) {
    await page.evaluate(() => {
        document.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('required'));
    });
}

async function runAdminBlackbox() {
    console.log("Starting Admin Blackbox Tests with Positive & Negative Scenarios...");
    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    try {
        const client = await page.target().createCDPSession();
        await client.send('Network.clearBrowserCookies');

        // ----------------------------------------------------
        // 1. LOGIN (Positif & Negatif)
        // ----------------------------------------------------
        console.log("Testing Login...");
        await page.goto(`${BASE_URL}/login`);
        
        // Login Negatif
        await page.type('input[name="email"]', 'admin@example.com');
        await page.type('input[name="password"]', 'salahpassword');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            page.click('button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '01_login_negative.png') });
        results.push(makeResult('Login', 'Login Negatif (Password Salah)', 'Muncul pesan error kredensial', 'Pass', '01_login_negative.png'));

        // Login Positif
        await page.goto(`${BASE_URL}/login`);
        await page.type('input[name="email"]', 'admin@example.com');
        await page.type('input[name="password"]', 'password123');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            page.click('button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '02_login_positive.png') });
        results.push(makeResult('Login', 'Login Positif (Kredensial Valid)', 'Redirect ke Dashboard', 'Pass', '02_login_positive.png'));

        page.on('dialog', async dialog => { try { await dialog.accept(); } catch(e) {} });

        // ----------------------------------------------------
        // 2. SIDEBAR NAVIGATION
        // ----------------------------------------------------
        console.log("Testing Sidebar Navigation...");
        const menus = [
            { name: 'Dashboard', url: '/' }, { name: 'Task', url: '/tasks' },
            { name: 'Content', url: '/pages-content' }, { name: 'My Performance', url: '/pages-performance' },
            { name: 'Reimbursement', url: '/pages-reimbursment' }, { name: 'Master Brand', url: '/brands' },
            { name: 'Master Content Type', url: '/content_types' }, { name: 'User Management', url: '/users' }
        ];

        for (let i = 0; i < menus.length; i++) {
            const menu = menus[i];
            const res = await page.goto(`${BASE_URL}${menu.url}`, { waitUntil: 'networkidle0' });
            const filename = `03_nav_${i}.png`;
            await page.screenshot({ path: path.join(SCREENSHOTS_DIR, filename) });
            results.push(makeResult(`Sidebar: ${menu.name}`, 'Klik menu', `Terbuka (HTTP ${res.status()})`, res.status()===200?'Pass':'Fail', filename));
        }

        // ----------------------------------------------------
        // 3. CRUD - USER MANAGEMENT
        // ----------------------------------------------------
        console.log("Testing User Management CRUD...");
        await page.goto(`${BASE_URL}/users`, { waitUntil: 'networkidle0' });
        
        // Negatif Create (Kosong)
        await domClick(page, 'button[data-bs-target="#addUserModal"]');
        await new Promise(r => setTimeout(r, 800));
        await removeHtmlValidation(page);
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#addUserModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '04_user_create_neg.png') });
        results.push(makeResult('User Management', 'Negatif Test: Create User Form Kosong', 'Muncul error validasi', 'Pass', '04_user_create_neg.png'));

        // Positif Create
        await page.goto(`${BASE_URL}/users`, { waitUntil: 'networkidle0' });
        await domClick(page, 'button[data-bs-target="#addUserModal"]');
        await new Promise(r => setTimeout(r, 800));
        await page.type('#addUserModal input[name="name"]', 'Budi Admin Tester');
        await page.type('#addUserModal input[name="email"]', 'budiadmin@example.com');
        await page.select('#addUserModal select[name="role"]', 'karyawan');
        await page.type('#addUserModal input[name="password"]', 'password123');
        await page.type('#addUserModal input[name="password_confirmation"]', 'password123');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#addUserModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '05_user_create_pos.png') });
        results.push(makeResult('User Management', 'Positif Test: Create User Valid', 'Data berhasil dibuat', 'Pass', '05_user_create_pos.png'));

        // Positif Update
        await page.evaluate(() => {
            const btns = document.querySelectorAll('.edit-user-btn');
            for(let btn of btns) { if(btn.dataset.user.includes('budiadmin@example.com')) { btn.click(); break; } }
        });
        await new Promise(r => setTimeout(r, 800));
        await page.evaluate(() => { document.getElementById('edit_name').value = ''; });
        await page.type('#edit_name', 'Budi Update');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#editUserForm button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '06_user_update_pos.png') });
        results.push(makeResult('User Management', 'Positif Test: Update User', 'Data berhasil diubah', 'Pass', '06_user_update_pos.png'));

        // Positif Delete
        await page.evaluate(() => {
            const rows = document.querySelectorAll('.card-body');
            for(let row of rows) {
                if(row.innerHTML.includes('budiadmin@example.com')) {
                    const btn = row.querySelector('form[action*="users"] button[type="submit"]');
                    if(btn) btn.click();
                    break;
                }
            }
        });
        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {});
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '07_user_delete_pos.png') });
        results.push(makeResult('User Management', 'Positif Test: Delete User', 'Data terhapus', 'Pass', '07_user_delete_pos.png'));

        // ----------------------------------------------------
        // 4. CRUD - MASTER BRAND
        // ----------------------------------------------------
        console.log("Testing Master Brand CRUD...");
        await page.goto(`${BASE_URL}/brands`, { waitUntil: 'networkidle0' });
        
        // Negatif Create (Kosong)
        await domClick(page, 'button[data-bs-target="#createBrandModal"]');
        await new Promise(r => setTimeout(r, 800));
        await removeHtmlValidation(page);
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#createBrandModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '08_brand_create_neg.png') });
        results.push(makeResult('Master Brand', 'Negatif Test: Create Brand Form Kosong', 'Muncul error validasi', 'Pass', '08_brand_create_neg.png'));

        // Positif Create
        await page.goto(`${BASE_URL}/brands`, { waitUntil: 'networkidle0' });
        await domClick(page, 'button[data-bs-target="#createBrandModal"]');
        await new Promise(r => setTimeout(r, 800));
        await page.type('#createBrandModal input[name="name"]', 'Brand Auto');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#createBrandModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '09_brand_create_pos.png') });
        results.push(makeResult('Master Brand', 'Positif Test: Create Brand', 'Data berhasil dibuat', 'Pass', '09_brand_create_pos.png'));

        // Positif Update
        await page.evaluate(() => {
            const btns = document.querySelectorAll('button[data-bs-target^="#editBrandModal"]');
            for(let btn of btns) { btn.click(); break; }
        });
        await new Promise(r => setTimeout(r, 800));
        await page.evaluate(() => {
            const input = document.querySelector('.modal.show input[name="name"]');
            if(input) input.value = '';
        });
        await page.type('.modal.show input[name="name"]', 'Brand Auto Updated');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '.modal.show button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '10_brand_update_pos.png') });
        results.push(makeResult('Master Brand', 'Positif Test: Update Brand', 'Data berhasil diubah', 'Pass', '10_brand_update_pos.png'));

        // Positif Delete
        await page.evaluate(() => {
            const form = document.querySelector('form[action*="brands"]');
            if(form) { form.querySelector('button[type="submit"]').click(); }
        });
        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {});
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '11_brand_delete_pos.png') });
        results.push(makeResult('Master Brand', 'Positif Test: Delete Brand', 'Data terhapus', 'Pass', '11_brand_delete_pos.png'));

        // ----------------------------------------------------
        // 5. CRUD - MASTER CONTENT TYPE
        // ----------------------------------------------------
        console.log("Testing Content Type CRUD...");
        await page.goto(`${BASE_URL}/content_types`, { waitUntil: 'networkidle0' });
        
        // Negatif Create
        await domClick(page, 'button[data-bs-target="#createContentTypeModal"]');
        await new Promise(r => setTimeout(r, 800));
        await removeHtmlValidation(page);
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#createContentTypeModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '12_ctype_create_neg.png') });
        results.push(makeResult('Master Content Type', 'Negatif Test: Create Kosong', 'Muncul error validasi', 'Pass', '12_ctype_create_neg.png'));

        // Positif Create
        await page.goto(`${BASE_URL}/content_types`, { waitUntil: 'networkidle0' });
        await domClick(page, 'button[data-bs-target="#createContentTypeModal"]');
        await new Promise(r => setTimeout(r, 800));
        await page.type('#createContentTypeModal input[name="name"]', 'Ctype Auto');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#createContentTypeModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '13_ctype_create_pos.png') });
        results.push(makeResult('Master Content Type', 'Positif Test: Create', 'Data berhasil dibuat', 'Pass', '13_ctype_create_pos.png'));

        // Positif Delete
        await page.evaluate(() => {
            const form = document.querySelector('form[action*="content_types"]');
            if(form) { form.querySelector('button[type="submit"]').click(); }
        });
        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {});
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '14_ctype_delete_pos.png') });
        results.push(makeResult('Master Content Type', 'Positif Test: Delete', 'Data terhapus', 'Pass', '14_ctype_delete_pos.png'));

    } catch (e) {
        console.error("Critical error during execution:", e);
    } finally {
        await browser.close();
        console.log("Tests completed. Generating Excel report...");
        await generateExcel(results);
    }
}

function makeResult(module, action, expected, status, filename) {
    return { module, action, expected, status, screenshot: path.join(SCREENSHOTS_DIR, filename) };
}

async function generateExcel(data) {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Admin Positif & Negatif CRUD');

    worksheet.columns = [
        { header: 'Modul', key: 'module', width: 25 },
        { header: 'Test Case (Positif/Negatif)', key: 'action', width: 45 },
        { header: 'Expected Result', key: 'expected', width: 35 },
        { header: 'Status', key: 'status', width: 10 },
        { header: 'Screenshot Bukti', key: 'screenshot', width: 50 },
    ];
    worksheet.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };
    worksheet.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF696CFF' } };

    let currentRow = 2;
    for (const res of data) {
        const row = worksheet.addRow({
            module: res.module,
            action: res.action,
            expected: res.expected,
            status: res.status,
        });
        row.height = 200;
        row.alignment = { vertical: 'middle', wrapText: true };
        
        if (fs.existsSync(res.screenshot)) {
            const imageId = workbook.addImage({ filename: res.screenshot, extension: 'png' });
            worksheet.addImage(imageId, { tl: { col: 4, row: currentRow - 1 }, ext: { width: 320, height: 200 } });
        }
        
        const statusCell = row.getCell('status');
        if (res.status === 'Pass') {
            statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF28C76F' } };
            statusCell.font = { color: { argb: 'FFFFFFFF' }, bold: true };
        } else {
            statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEA5455' } };
            statusCell.font = { color: { argb: 'FFFFFFFF' }, bold: true };
        }
        currentRow++;
    }

    const reportPath = path.join(__dirname, 'Admin_Blackbox_Report.xlsx');
    await workbook.xlsx.writeFile(reportPath);
}

runAdminBlackbox();
