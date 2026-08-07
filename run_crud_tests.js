const puppeteer = require('puppeteer');
const ExcelJS = require('exceljs');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8888';

const testCases = [
    { id: 'TC-CRUD-01', action: 'CREATE', expected: 'Gagal Create (Validasi Form Kosong/Invalid)', description: 'Mencoba membuat user dengan form kosong' },
    { id: 'TC-CRUD-02', action: 'CREATE', expected: 'Berhasil Create User Baru', description: 'Mengisi form Add User dengan data valid dan submit' },
    { id: 'TC-CRUD-03', action: 'READ', expected: 'Data User Baru Tampil di Tabel', description: 'Melihat user baru di daftar list user' },
    { id: 'TC-CRUD-04', action: 'UPDATE', expected: 'Gagal Update (Validasi Email Duplikat)', description: 'Mencoba mengubah email menjadi milik orang lain' },
    { id: 'TC-CRUD-05', action: 'UPDATE', expected: 'Berhasil Update Nama User', description: 'Mengubah nama user baru dan submit' },
    { id: 'TC-CRUD-06', action: 'DELETE', expected: 'Berhasil Hapus User', description: 'Menghapus user baru yang dibuat' },
];

async function runCrudTests() {
    console.log("Starting Full CRUD Blackbox Tests on User Management...");
    const screenshotsDir = path.join(__dirname, 'screenshots');
    if (!fs.existsSync(screenshotsDir)){
        fs.mkdirSync(screenshotsDir);
    }

    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    const results = [];

    try {
        console.log("Logging in as Admin...");
        await page.goto(`${BASE_URL}/login`);
        await page.type('input[name="email"]', 'admin@example.com');
        await page.type('input[name="password"]', 'password123');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('button[type="submit"]')
        ]);

        console.log("Navigating to User Management...");
        await page.goto(`${BASE_URL}/users`, { waitUntil: 'networkidle0' });

        // Accept any dialogs (for delete confirmation)
        page.on('dialog', async dialog => {
            await dialog.accept();
        });

        // ----------------------------------------------------
        // TC-CRUD-01: CREATE FAIL (Empty)
        // ----------------------------------------------------
        let tc = testCases[0];
        console.log(`Running ${tc.id}: ${tc.description}`);
        await page.click('button[data-bs-target="#addUserModal"]');
        await new Promise(r => setTimeout(r, 1000)); // wait for modal
        
        // Remove HTML5 validation to test server response or just trigger HTML5 error
        await page.evaluate(() => {
            document.querySelectorAll('#addUserModal input').forEach(el => el.removeAttribute('required'));
        });
        
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('#addUserModal button[type="submit"]')
        ]);
        
        await page.screenshot({ path: path.join(screenshotsDir, `${tc.id}.png`) });
        let html = await page.content();
        results.push({
            ...tc,
            actual: html.includes('The name field is required') || html.includes('alert-danger') ? 'Muncul pesan error validasi (Gagal)' : 'Tidak ada pesan error validasi',
            status: html.includes('The name field is required') || html.includes('alert-danger') ? 'Pass' : 'Fail',
            screenshot: path.join(screenshotsDir, `${tc.id}.png`)
        });

        // ----------------------------------------------------
        // TC-CRUD-02: CREATE SUCCESS
        // ----------------------------------------------------
        tc = testCases[1];
        console.log(`Running ${tc.id}: ${tc.description}`);
        await page.goto(`${BASE_URL}/users`, { waitUntil: 'networkidle0' });
        await page.click('button[data-bs-target="#addUserModal"]');
        await new Promise(r => setTimeout(r, 1000));
        
        await page.type('#addUserModal input[name="name"]', 'Budi CRUD Tester');
        await page.type('#addUserModal input[name="email"]', 'budicrud@example.com');
        await page.select('#addUserModal select[name="role"]', 'karyawan');
        await page.type('#addUserModal input[name="password"]', 'password123');
        await page.type('#addUserModal input[name="password_confirmation"]', 'password123');
        
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('#addUserModal button[type="submit"]')
        ]);

        await page.screenshot({ path: path.join(screenshotsDir, `${tc.id}.png`) });
        html = await page.content();
        results.push({
            ...tc,
            actual: html.includes('User created successfully') ? 'Muncul alert sukses (Berhasil)' : 'Tidak muncul alert sukses',
            status: html.includes('User created successfully') ? 'Pass' : 'Fail',
            screenshot: path.join(screenshotsDir, `${tc.id}.png`)
        });

        // ----------------------------------------------------
        // TC-CRUD-03: READ SUCCESS
        // ----------------------------------------------------
        tc = testCases[2];
        console.log(`Running ${tc.id}: ${tc.description}`);
        await page.screenshot({ path: path.join(screenshotsDir, `${tc.id}.png`) });
        html = await page.content();
        results.push({
            ...tc,
            actual: html.includes('Budi CRUD Tester') ? 'Data "Budi CRUD Tester" terlihat di tabel' : 'Data tidak ditemukan di tabel',
            status: html.includes('Budi CRUD Tester') ? 'Pass' : 'Fail',
            screenshot: path.join(screenshotsDir, `${tc.id}.png`)
        });

        // ----------------------------------------------------
        // TC-CRUD-04: UPDATE FAIL (Duplicate Email)
        // ----------------------------------------------------
        tc = testCases[3];
        console.log(`Running ${tc.id}: ${tc.description}`);
        // Find edit button for our user
        await page.evaluate(() => {
            const buttons = document.querySelectorAll('.edit-user-btn');
            for(let btn of buttons) {
                if(btn.dataset.user && btn.dataset.user.includes('budicrud@example.com')) {
                    btn.click();
                    break;
                }
            }
        });
        await new Promise(r => setTimeout(r, 1000));
        
        // Set email to admin@example.com
        await page.evaluate(() => { document.getElementById('edit_email').value = ''; });
        await page.type('#edit_email', 'admin@example.com');
        
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('#editUserForm button[type="submit"]')
        ]);

        await page.screenshot({ path: path.join(screenshotsDir, `${tc.id}.png`) });
        html = await page.content();
        results.push({
            ...tc,
            actual: html.includes('has already been taken') || html.includes('alert-danger') ? 'Pesan error email sudah digunakan muncul' : 'Gagal memvalidasi duplikat',
            status: html.includes('has already been taken') || html.includes('alert-danger') ? 'Pass' : 'Fail',
            screenshot: path.join(screenshotsDir, `${tc.id}.png`)
        });

        // ----------------------------------------------------
        // TC-CRUD-05: UPDATE SUCCESS
        // ----------------------------------------------------
        tc = testCases[4];
        console.log(`Running ${tc.id}: ${tc.description}`);
        await page.goto(`${BASE_URL}/users`, { waitUntil: 'networkidle0' });
        await page.evaluate(() => {
            const buttons = document.querySelectorAll('.edit-user-btn');
            for(let btn of buttons) {
                if(btn.dataset.user && btn.dataset.user.includes('budicrud@example.com')) {
                    btn.click();
                    break;
                }
            }
        });
        await new Promise(r => setTimeout(r, 1000));
        
        await page.evaluate(() => { document.getElementById('edit_name').value = ''; });
        await page.type('#edit_name', 'Budi CRUD Updated');
        
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('#editUserForm button[type="submit"]')
        ]);

        await page.screenshot({ path: path.join(screenshotsDir, `${tc.id}.png`) });
        html = await page.content();
        results.push({
            ...tc,
            actual: html.includes('User updated successfully') ? 'Muncul alert update sukses (Berhasil)' : 'Tidak muncul alert sukses',
            status: html.includes('User updated successfully') ? 'Pass' : 'Fail',
            screenshot: path.join(screenshotsDir, `${tc.id}.png`)
        });

        // ----------------------------------------------------
        // TC-CRUD-06: DELETE SUCCESS
        // ----------------------------------------------------
        tc = testCases[5];
        console.log(`Running ${tc.id}: ${tc.description}`);
        await page.evaluate(() => {
            // Find delete button for the user
            const rows = document.querySelectorAll('.card-body');
            for(let row of rows) {
                if(row.innerHTML.includes('budicrud@example.com')) {
                    const deleteForm = row.querySelector('form[action*="users"]');
                    if (deleteForm) {
                        deleteForm.querySelector('button[type="submit"]').click();
                    }
                    break;
                }
            }
        });
        
        await page.waitForNavigation({ waitUntil: 'networkidle0' });

        await page.screenshot({ path: path.join(screenshotsDir, `${tc.id}.png`) });
        html = await page.content();
        results.push({
            ...tc,
            actual: html.includes('User deleted successfully') ? 'Muncul alert hapus sukses (Berhasil)' : 'Tidak muncul alert sukses',
            status: html.includes('User deleted successfully') ? 'Pass' : 'Fail',
            screenshot: path.join(screenshotsDir, `${tc.id}.png`)
        });

    } catch (error) {
        console.error("Test execution failed:", error);
    }

    await browser.close();
    console.log("Tests completed. Generating Excel report...");
    await generateExcel(results);
}

async function generateExcel(results) {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('CRUD Testing Results');

    worksheet.columns = [
        { header: 'Test Case ID', key: 'id', width: 15 },
        { header: 'Aksi', key: 'action', width: 15 },
        { header: 'Deskripsi', key: 'description', width: 35 },
        { header: 'Expected Result', key: 'expected', width: 35 },
        { header: 'Actual Result', key: 'actual', width: 35 },
        { header: 'Status', key: 'status', width: 10 },
        { header: 'Screenshot', key: 'screenshot', width: 50 },
    ];

    worksheet.getRow(1).font = { bold: true };
    
    let currentRow = 2;
    for (const res of results) {
        const row = worksheet.addRow({
            id: res.id,
            action: res.action,
            description: res.description,
            expected: res.expected,
            actual: res.actual,
            status: res.status,
        });

        row.height = 200;
        
        if (fs.existsSync(res.screenshot)) {
            const imageId = workbook.addImage({
                filename: res.screenshot,
                extension: 'png',
            });
            
            worksheet.addImage(imageId, {
                tl: { col: 6, row: currentRow - 1 },
                ext: { width: 320, height: 200 }
            });
        }
        
        const statusCell = row.getCell('status');
        if (res.status === 'Pass') {
            statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF00FF00' } };
        } else {
            statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFF0000' } };
        }
        
        currentRow++;
    }

    const reportPath = path.join(__dirname, 'CRUD_Testing_Report.xlsx');
    await workbook.xlsx.writeFile(reportPath);
    console.log(`Excel report saved successfully at: ${reportPath}`);
}

runCrudTests();
