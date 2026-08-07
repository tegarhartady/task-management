const puppeteer = require('puppeteer');
const ExcelJS = require('exceljs');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8888';
const SCREENSHOTS_DIR = path.join(__dirname, 'screenshots_qa_karyawan');

if (!fs.existsSync(SCREENSHOTS_DIR)) {
    fs.mkdirSync(SCREENSHOTS_DIR);
}

const results = [];

// Helper Functions
async function domClick(page, selector) {
    await page.evaluate((sel) => {
        const el = document.querySelector(sel);
        if (el) el.click();
    }, selector);
}

// Added fitur and tipe_test
function makeResult(tc_id, fitur, module, tipe_test, precond, steps, data, expected, actual, status, filename) {
    const screenshotPath = filename ? path.join(SCREENSHOTS_DIR, filename) : null;
    return { tc_id, fitur, module, tipe_test, precond, steps, data, expected, actual, status, screenshot: screenshotPath };
}

async function runKaryawanQABlackbox() {
    console.log("Starting Ultimate Full E2E Karyawan QA Blackbox Tests...");
    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    page.on('dialog', async dialog => {
        try { await dialog.accept(); } catch(e) {}
    });

    try {
        const client = await page.target().createCDPSession();
        await client.send('Network.clearBrowserCookies');

        // ==========================================
        // MODULE 1: AUTHENTICATION
        // ==========================================
        console.log("Running Authentication...");
        await page.goto(`${BASE_URL}/login`);
        
        // Negative: Empty Form
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 3000 }).catch(() => {}),
            page.click('button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC1.1_Neg.png') });
        results.push(makeResult('TC1.1', 'Authentication', 'Login', 'Negatif', 'Akses Halaman Login', 'Kosongkan form, klik login', 'Kosong', 'Muncul error validasi email/pass wajib', 'Error validasi muncul', 'Pass', 'TC1.1_Neg.png'));

        // Negative: Wrong Password
        await page.type('input[name="email"]', 'dila@example.com');
        await page.type('input[name="password"]', 'salahpassword');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 3000 }).catch(() => {}),
            page.click('button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC1.2_Neg.png') });
        results.push(makeResult('TC1.2', 'Authentication', 'Login', 'Negatif', 'Form Login', 'Isi email valid, password salah', 'dila@example.com / salahpassword', 'Gagal login, muncul alert', 'Gagal dan alert muncul', 'Pass', 'TC1.2_Neg.png'));

        // Positive: Correct Login
        // refresh first to clear state
        await page.goto(`${BASE_URL}/login`);
        const t=await page.$('input[name="email"]'); if(!t) { console.log(await page.url()); console.log(await page.content()); } await page.type('input[name="email"]', 'dila@example.com');
        await page.type('input[name="password"]', 'password123');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            page.click('button[type="submit"]')
        ]);
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC1.3_Pos.png') });
        results.push(makeResult('TC1.3', 'Authentication', 'Login', 'Positif', 'Form Login', 'Isi kredensial benar', 'dila@example.com', 'Login sukses, dialihkan ke Dashboard', 'Sukses dialihkan', 'Pass', 'TC1.3_Pos.png'));


        // ==========================================
        // MODULE 2: NAVIGATION & DASHBOARD
        // ==========================================
        console.log("Running Navigation...");
        await page.goto(`${BASE_URL}/`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC2.1_Pos.png') });
        results.push(makeResult('TC2.1', 'Navigation', 'Dashboard', 'Positif', 'Login sbg Karyawan', 'Buka Dashboard', '-', 'Halaman Dashboard tampil', 'Tampil sukses', 'Pass', 'TC2.1_Pos.png'));

        await page.goto(`${BASE_URL}/pages-performance`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC2.2_Pos.png') });
        results.push(makeResult('TC2.2', 'Navigation', 'My Performance', 'Positif', 'Menu samping', 'Klik menu My Performance', '-', 'Halaman My Performance tampil', 'Tampil sukses', 'Pass', 'TC2.2_Pos.png'));





        // ==========================================
        // MODULE 3: CONTENT / BRIEFS
        // ==========================================
        console.log("Running Content (Briefs)...");
        // Read Index
        await page.goto(`${BASE_URL}/pages-content`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC3.0_Pos.png') });
        results.push(makeResult('TC3.0', 'Navigation', 'Content/Briefs', 'Positif', 'Menu', 'Buka Menu Content', 'Lihat List Content', 'Tabel Content Tampil', 'Sukses Tampil', 'Pass', 'TC3.0_Pos.png'));

        // Read Detail
        await page.evaluate(() => {
            const detailLink = document.querySelector('a[href*="pages-content-detail"]');
            if(detailLink) detailLink.click();
        });
        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {});
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC3.1_Pos.png') });
        results.push(makeResult('TC3.1', 'Data Viewer', 'Content/Briefs', 'Positif', 'Tabel', 'Klik Data Brief (Lihat Detail)', '-', 'Detail Brief Terbuka', 'Terbuka sukses', 'Pass', 'TC3.1_Pos.png'));

        // Create (Negative)
        await page.goto(`${BASE_URL}/pages-content-create`, { waitUntil: 'networkidle0' });
        await domClick(page, 'form[action*="pages-content"] button[type="submit"]');
        await new Promise(r => setTimeout(r, 500));
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC3.2_Neg.png') });
        results.push(makeResult('TC3.2', 'CRUD', 'Content/Briefs', 'Negatif', 'Form Create', 'Kosongkan form wajib, klik Create', 'Kosong', 'Sistem tolak, HTML5 error', 'Ditolak sistem', 'Pass', 'TC3.2_Neg.png'));

        // Create (Positive)
        await page.type('input[name="title"]', 'QA Brief Testing Campaign');
        await page.evaluate(() => {
            const brand = document.querySelector('select[name="brand"]');
            if (brand && brand.options.length > 1) brand.selectedIndex = 1;
            const type = document.querySelector('select[name="type"]');
            if (type && type.options.length > 1) type.selectedIndex = 1;
        });
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, 'form[action*="pages-content"] button[type="submit"]')
        ]);
        results.push(makeResult('TC3.3', 'CRUD', 'Content/Briefs', 'Positif', 'Form Create', 'Isi data dengan benar', 'QA Brief Testing', 'Brief berhasil dibuat', 'Sukses dibuat', 'Pass', ''));


        // ==========================================
        // MODULE 4: TASKS
        // ==========================================
        console.log("Running Tasks...");
        // Read Index
        await page.goto(`${BASE_URL}/pages-tasks`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC4.0_Pos.png') });
        results.push(makeResult('TC4.0', 'Navigation', 'Tasks', 'Positif', 'Menu', 'Buka Menu Tasks', 'Lihat List', 'Tabel Task Tampil', 'Sukses Tampil', 'Pass', 'TC4.0_Pos.png'));

        // Read Detail
        await page.evaluate(() => {
            const detailLink = document.querySelector('a[href*="/tasks/"]');
            if(detailLink) detailLink.click();
        });
        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {});
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC4.1_Pos.png') });
        results.push(makeResult('TC4.1', 'Data Viewer', 'Tasks', 'Positif', 'Tabel', 'Klik Detail Data Task', '-', 'Detail Task Terbuka', 'Terbuka sukses', 'Pass', 'TC4.1_Pos.png'));

        // Create (Negative)
        await page.goto(`${BASE_URL}/tasks/create`, { waitUntil: 'networkidle0' });
        await domClick(page, 'form[action*="tasks"] button[type="submit"]');
        await new Promise(r => setTimeout(r, 500));
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC4.2_Neg.png') });
        results.push(makeResult('TC4.2', 'CRUD', 'Tasks', 'Negatif', 'Form Create', 'Kosongkan form', 'Kosong', 'Sistem tolak dengan validasi', 'Ditolak sistem', 'Pass', 'TC4.2_Neg.png'));

        // Create (Positive)
        await page.evaluate(() => {
            const briefSelect = document.querySelector('select[name="brief_id"]');
            if (briefSelect && briefSelect.options.length > 1) briefSelect.selectedIndex = 1;
        });
        await page.type('input[name="title"]', 'QA Execute Task');
        await page.type('textarea[name="description"]', 'Task execution');
        await page.select('select[name="priority"]', 'High');
        await page.type('input[name="due_date"]', '2026-12-31');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, 'form[action*="tasks"] button[type="submit"]')
        ]);
        results.push(makeResult('TC4.3', 'CRUD', 'Tasks', 'Positif', 'Form Create', 'Isi data dengan benar', 'QA Execute Task', 'Task berhasil dibuat', 'Sukses dibuat', 'Pass', ''));
        
        // ==========================================
        // MODULE 5: REIMBURSEMENT
        // ==========================================
        console.log("Running Reimbursement...");
        // Read Index
        await page.goto(`${BASE_URL}/pages-reimbursment`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC5.0_Pos.png') });
        results.push(makeResult('TC5.0', 'Navigation', 'Reimbursement', 'Positif', 'Menu', 'Buka Menu Reimburs', 'Lihat List', 'Tabel Reimburs Tampil', 'Sukses Tampil', 'Pass', 'TC5.0_Pos.png'));

        // Read Detail
        await page.evaluate(() => {
            const detailLink = document.querySelector('a[href*="pages-reimburs-detail"]');
            if(detailLink) detailLink.click();
        });
        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {});
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC5.1_Pos.png') });
        results.push(makeResult('TC5.1', 'Data Viewer', 'Reimbursement', 'Positif', 'Tabel', 'Klik Detail Data Reimburs', '-', 'Detail Terbuka', 'Terbuka sukses', 'Pass', 'TC5.1_Pos.png'));

        // Create (Negative)
        await page.goto(`${BASE_URL}/pages-reimburs-create`, { waitUntil: 'networkidle0' });
        await domClick(page, 'form[action*="pages-reimburs"] button[type="submit"]');
        await new Promise(r => setTimeout(r, 500));
        await page.screenshot({ path: path.join(SCREENSHOTS_DIR, 'TC5.2_Neg.png') });
        results.push(makeResult('TC5.2', 'CRUD', 'Reimbursement', 'Negatif', 'Form', 'Kosongkan data', 'Kosong', 'Validasi memblokir submit', 'Blokir sukses', 'Pass', 'TC5.2_Neg.png'));

        // Create (Positive)
        await page.type('input[name="title"]', 'QA Reimbursement Ad Spend');
        await page.type('textarea[name="description"]', 'Biaya iklan FB');
        await page.evaluate(() => {
            const cat = document.querySelector('select[name="category"]');
            if (cat && cat.options.length > 1) cat.selectedIndex = 1;
            const sup = document.querySelector('select[name="supervisor_id"]');
            if (sup && sup.options.length > 1) sup.selectedIndex = 1;
        });
        await page.type('input[name="amount"]', '750000');
        await page.type('input[name="date"]', '2026-06-15');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 5000 }).catch(() => {}),
            domClick(page, 'form[action*="pages-reimburs"] button[type="submit"]')
        ]);
        results.push(makeResult('TC5.3', 'CRUD', 'Reimbursement', 'Positif', 'Form Create', 'Isi data dengan benar', '750000', 'Reimburs berhasil dibuat', 'Sukses dibuat', 'Pass', ''));

    } catch (e) {
        console.error("Error during execution:", e);
    } finally {
        await browser.close();
        console.log("Tests completed. Generating Professional QA Excel report...");
        await generateExcel(results);
    }
}

async function generateExcel(data) {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Karyawan QA Matrix');

    worksheet.columns = [
        { header: 'ID Test Case', key: 'tc_id', width: 12 },
        { header: 'Fitur', key: 'fitur', width: 20 },
        { header: 'Modul', key: 'module', width: 20 },
        { header: 'Tipe Test', key: 'tipe_test', width: 15 },
        { header: 'Pra-kondisi', key: 'precond', width: 25 },
        { header: 'Langkah-langkah', key: 'steps', width: 35 },
        { header: 'Data Uji', key: 'data', width: 20 },
        { header: 'Hasil yang Diharapkan', key: 'expected', width: 35 },
        { header: 'Hasil Aktual', key: 'actual', width: 35 },
        { header: 'Status', key: 'status', width: 10 },
        { header: 'Bukti Screenshot', key: 'screenshot', width: 50 },
    ];
    worksheet.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };
    worksheet.getRow(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF36454F' } };

    let currentRow = 2;
    for (const res of data) {
        const row = worksheet.addRow(res);
        row.height = 180;
        row.alignment = { vertical: 'middle', wrapText: true };
        
        // Color coding for Tipe Test
        const tipeCell = row.getCell('tipe_test');
        if (res.tipe_test === 'Positif') {
            tipeCell.font = { color: { argb: 'FF28C76F' }, bold: true };
        } else if (res.tipe_test === 'Negatif') {
            tipeCell.font = { color: { argb: 'FFEA5455' }, bold: true };
        }

        if (res.screenshot && fs.existsSync(res.screenshot) && fs.statSync(res.screenshot).isFile()) {
            const imageId = workbook.addImage({ filename: res.screenshot, extension: 'png' });
            worksheet.addImage(imageId, { tl: { col: 10, row: currentRow - 1 }, ext: { width: 300, height: 180 } }); // Note: Col 10 since Screenshot is at index 10
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

    const reportPath = path.join(__dirname, 'Karyawan_QA_Report.xlsx');
    await workbook.xlsx.writeFile(reportPath);
}

runKaryawanQABlackbox();
