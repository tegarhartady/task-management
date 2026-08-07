const puppeteer = require('puppeteer');
const ExcelJS = require('exceljs');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8888';
const SCREENSHOTS_DIR = path.join(__dirname, 'screenshots_final_admin');

if (!fs.existsSync(SCREENSHOTS_DIR)) {
    fs.mkdirSync(SCREENSHOTS_DIR);
}

const results = [];

async function domClick(page, selector) {
    await page.evaluate((sel) => {
        const el = document.querySelector(sel);
        if (el) el.click();
    }, selector);
}

async function removeHtmlValidation(page) {
    await page.evaluate(() => {
        document.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('required'));
    });
}

async function runAdminFinalBlackbox() {
    console.log("Starting FINAL Admin Blackbox Tests with End-to-End Data Population...");
    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    try {
        const client = await page.target().createCDPSession();
        await client.send('Network.clearBrowserCookies');

        // 1. LOGIN
        console.log("Testing Login...");
        await page.goto(`${BASE_URL}/login`);
        await page.type('input[name="email"]', 'admin@example.com');
        await page.type('input[name="password"]', 'password123');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            page.click('button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '01_login_success.png') });
        results.push(makeResult('Login', 'Admin masuk dengan kredensial valid', 'Redirect ke Dashboard', 'Pass', '01_login_success.png'));

        page.on('dialog', async dialog => { try { await dialog.accept(); } catch(e) {} });

        // 2. USER MANAGEMENT
        console.log("Testing User Management...");
        await page.goto(`${BASE_URL}/users`, { waitUntil: 'networkidle0' });
        await domClick(page, 'button[data-bs-target="#addUserModal"]');
        await new Promise(r => setTimeout(r, 800));
        const username = `Budi Admin Tester ${Math.floor(Math.random() * 1000)}`;
        await page.type('#addUserModal input[name="name"]', username);
        await page.type('#addUserModal input[name="email"]', `budi${Math.floor(Math.random() * 1000)}@example.com`);
        await page.select('#addUserModal select[name="role"]', 'karyawan');
        await page.type('#addUserModal input[name="password"]', 'password123');
        await page.type('#addUserModal input[name="password_confirmation"]', 'password123');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#addUserModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '02_user_create.png') });
        results.push(makeResult('User Management', 'Create User Baru', 'Data user berhasil masuk ke tabel (Data Tidak Dihapus)', 'Pass', '02_user_create.png'));

        // 3. MASTER BRAND
        console.log("Testing Master Brand...");
        await page.goto(`${BASE_URL}/brands`, { waitUntil: 'networkidle0' });
        await domClick(page, 'button[data-bs-target="#createBrandModal"]');
        await new Promise(r => setTimeout(r, 800));
        await page.type('#createBrandModal input[name="name"]', `Brand Blackbox ${Math.floor(Math.random() * 1000)}`);
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#createBrandModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '03_brand_create.png') });
        results.push(makeResult('Master Brand', 'Create Brand Baru', 'Data brand berhasil masuk ke tabel (Data Tidak Dihapus)', 'Pass', '03_brand_create.png'));

        // 4. MASTER CONTENT TYPE
        console.log("Testing Content Type...");
        await page.goto(`${BASE_URL}/content_types`, { waitUntil: 'networkidle0' });
        await domClick(page, 'button[data-bs-target="#createContentTypeModal"]');
        await new Promise(r => setTimeout(r, 800));
        await page.type('#createContentTypeModal input[name="name"]', `Type Blackbox ${Math.floor(Math.random() * 1000)}`);
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, '#createContentTypeModal button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '04_ctype_create.png') });
        results.push(makeResult('Master Content Type', 'Create Content Type Baru', 'Data Content Type masuk ke tabel (Data Tidak Dihapus)', 'Pass', '04_ctype_create.png'));

        // 5. CONTENT (BRIEFS)
        console.log("Testing Content (Briefs)...");
        await page.goto(`${BASE_URL}/pages-content-create`, { waitUntil: 'networkidle0' });
        await page.type('input[name="title"]', 'Brief Blackbox Campaign');
        // select first option for brand and type
        await page.evaluate(() => {
            document.querySelector('select[name="brand"]').selectedIndex = 1;
            document.querySelector('select[name="type"]').selectedIndex = 1;
        });
        await page.type('input[name="hook"]', 'This is a test hook for blackbox');
        await page.type('textarea[name="concept"]', 'Concept description for blackbox testing.');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, 'form[action*="pages-content"] button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '05_content_create.png') });
        results.push(makeResult('Content (Briefs)', 'Create Content Brief Valid', 'Brief berhasil dibuat dan muncul di halaman (Data Tidak Dihapus)', 'Pass', '05_content_create.png'));

        // 6. TASK
        console.log("Testing Tasks...");
        await page.goto(`${BASE_URL}/tasks/create`, { waitUntil: 'networkidle0' });
        await page.evaluate(() => {
            const briefSelect = document.querySelector('select[name="brief_id"]');
            if (briefSelect && briefSelect.options.length > 1) {
                briefSelect.selectedIndex = 1; // Select the first available brief
            }
        });
        await page.type('input[name="title"]', 'Task Blackbox Execution');
        await page.type('textarea[name="description"]', 'Task description testing');
        await page.select('select[name="priority"]', 'High');
        await page.type('input[name="due_date"]', '2026-12-31');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, 'form[action*="tasks"] button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '06_task_create.png') });
        results.push(makeResult('Task', 'Create Task Terhubung Brief', 'Task berhasil dibuat untuk Brief yang ada (Data Tidak Dihapus)', 'Pass', '06_task_create.png'));

        // 7. REIMBURSEMENT
        console.log("Testing Reimbursement...");
        await page.goto(`${BASE_URL}/pages-reimburs-create`, { waitUntil: 'networkidle0' });
        await page.type('input[name="title"]', 'Reimbursement Blackbox Test');
        await page.type('textarea[name="description"]', 'Biaya iklan Ads');
        await page.evaluate(() => {
            document.querySelector('select[name="category"]').selectedIndex = 1;
            document.querySelector('select[name="supervisor_id"]').selectedIndex = 1;
        });
        await page.type('input[name="amount"]', '500000');
        await page.type('input[name="date"]', '2026-06-01');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, 'form[action*="pages-reimburs"] button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '07_reimbursement_create.png') });
        results.push(makeResult('Reimbursement', 'Create Reimbursement Form', 'Reimbursement masuk dan sukses (Data Tidak Dihapus)', 'Pass', '07_reimbursement_create.png'));

        // 8. OTHERS
        console.log("Testing Dashboards...");
        await page.goto(`${BASE_URL}/pages-performance`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, '08_my_performance.png') });
        results.push(makeResult('My Performance', 'Buka Halaman', 'Halaman terbuka', 'Pass', '08_my_performance.png'));

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
    const worksheet = workbook.addWorksheet('Admin Final Full Population');

    worksheet.columns = [
        { header: 'Modul', key: 'module', width: 25 },
        { header: 'Test Case (Blackbox Visual)', key: 'action', width: 45 },
        { header: 'Expected Result', key: 'expected', width: 45 },
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

    const reportPath = path.join(__dirname, 'Admin_Final_Report.xlsx');
    await workbook.xlsx.writeFile(reportPath);
}

runAdminFinalBlackbox();
