const puppeteer = require('puppeteer');
const ExcelJS = require('exceljs');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8888';

const users = [
    { role: 'admin', email: 'admin@example.com' },
    { role: 'supervisor', email: 'supervisor@example.com' },
    { role: 'karyawan', email: 'dila@example.com' }
];

const menus = [
    { url: '/admin-dashboard', name: 'Admin Dashboard', allowedRoles: ['admin'] },
    { url: '/supervisor-dashboard', name: 'Supervisor Dashboard', allowedRoles: ['supervisor'] },
    { url: '/manager-dashboard', name: 'Manager Dashboard', allowedRoles: ['manager'] },
    { url: '/', name: 'Karyawan Home', allowedRoles: ['admin', 'supervisor', 'manager', 'karyawan'] }, // No middleware role
    { url: '/tasks', name: 'Task', allowedRoles: ['admin', 'supervisor', 'manager', 'karyawan'] },
    { url: '/pages-content', name: 'Content', allowedRoles: ['admin', 'supervisor', 'manager', 'karyawan'] },
    { url: '/pages-performance', name: 'My Performance', allowedRoles: ['admin', 'supervisor', 'manager', 'karyawan'] },
    { url: '/pages-reimbursment', name: 'Reimbursement', allowedRoles: ['admin', 'supervisor', 'manager', 'karyawan'] },
    { url: '/brands', name: 'Master Brand', allowedRoles: ['admin'] },
    { url: '/content_types', name: 'Master Content Type', allowedRoles: ['admin'] },
    { url: '/users', name: 'User Management', allowedRoles: ['admin'] },
];

async function runTests() {
    console.log("Starting Full Matrix Blackbox Tests...");
    const screenshotsDir = path.join(__dirname, 'screenshots');
    if (!fs.existsSync(screenshotsDir)){
        fs.mkdirSync(screenshotsDir);
    }

    const browser = await puppeteer.launch({ headless: 'new' });
    const results = [];
    let tcCount = 1;

    for (const user of users) {
        console.log(`\n--- Testing as ${user.role.toUpperCase()} ---`);
        const page = await browser.newPage();
        await page.setViewport({ width: 1280, height: 800 });

        try {
            const client = await page.target().createCDPSession();
            await client.send('Network.clearBrowserCookies');

            console.log(`Logging in as ${user.email}...`);
            await page.goto(`${BASE_URL}/login`);
            await page.type('input[name="email"]', user.email);
            await page.type('input[name="password"]', 'password123');
            
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle0' }),
                page.click('button[type="submit"]')
            ]);

            const allowedMenus = menus.filter(m => m.allowedRoles.includes(user.role));

            for (const menu of allowedMenus) {
                const tcId = `TC-${String(tcCount).padStart(3, '0')}`;
                console.log(`  Running ${tcId} : ${menu.name} (${menu.url})`);

                const expected = 'Bisa mengakses halaman (200 OK)';
                
                let actualResult = '';
                let status = 'Fail';
                const screenshotPath = path.join(screenshotsDir, `${tcId}.png`);

                const response = await page.goto(`${BASE_URL}${menu.url}`, { waitUntil: 'networkidle0' });
                await new Promise(r => setTimeout(r, 800)); // wait for render

                const text = await page.evaluate(() => document.body.innerText);
                if (text.includes('403') || text.includes('Unauthorized') || response.status() === 403) {
                    actualResult = 'Akses ditolak (403 Forbidden)';
                } else if (response.status() === 200) {
                    actualResult = 'Bisa mengakses halaman (200 OK)';
                } else {
                    actualResult = `Status code: ${response.status()}`;
                }

                if (actualResult.includes('200 OK')) {
                    status = 'Pass';
                } else {
                    status = 'Fail';
                }

                await page.screenshot({ path: screenshotPath });

                results.push({
                    id: tcId,
                    role: user.role,
                    menuName: menu.name,
                    url: menu.url,
                    expected,
                    actual: actualResult,
                    status,
                    screenshotPath
                });

                tcCount++;
            }
        } catch (error) {
            console.error(`Error for user ${user.role}: ${error.message}`);
        }
        await page.close();
    }

    await browser.close();
    console.log("\nTests completed. Generating Excel report...");
    await generateExcel(results);
}

async function generateExcel(results) {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Blackbox Full Matrix');

    worksheet.columns = [
        { header: 'Test Case ID', key: 'id', width: 15 },
        { header: 'Role', key: 'role', width: 15 },
        { header: 'Menu Name', key: 'menuName', width: 25 },
        { header: 'URL', key: 'url', width: 25 },
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
            role: res.role,
            menuName: res.menuName,
            url: res.url,
            expected: res.expected,
            actual: res.actual,
            status: res.status,
        });

        row.height = 200;
        
        if (fs.existsSync(res.screenshotPath)) {
            const imageId = workbook.addImage({
                filename: res.screenshotPath,
                extension: 'png',
            });
            
            worksheet.addImage(imageId, {
                tl: { col: 7, row: currentRow - 1 }, // Column H (0-indexed 7)
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

    const reportPath = path.join(__dirname, 'Blackbox_Testing_Report_Allowed_Only.xlsx');
    await workbook.xlsx.writeFile(reportPath);
    console.log(`Excel report saved successfully at: ${reportPath}`);
}

runTests();
