const puppeteer = require('puppeteer');
const fs = require('fs');

// Helpers for screenshots
async function takeScreenshot(page, role, feature, action, type) {
    const dir = `./qa_reports/${role}`;
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    
    // Inject header HTML
    await page.evaluate(({role, feature, type}) => {
        const header = document.createElement('div');
        header.id = 'qa-test-header';
        header.style.position = 'fixed';
        header.style.top = '0';
        header.style.left = '0';
        header.style.width = '100%';
        header.style.backgroundColor = type === 'Positif' ? '#28a745' : '#dc3545';
        header.style.color = '#fff';
        header.style.padding = '15px';
        header.style.textAlign = 'center';
        header.style.fontSize = '24px';
        header.style.fontWeight = 'bold';
        header.style.zIndex = '999999';
        header.style.boxShadow = '0 4px 6px rgba(0,0,0,0.3)';
        header.innerText = `Role: ${role.toUpperCase()} | Fitur: ${feature} | Tipe Test: ${type}`;
        
        // Push body down so header doesn't overlap
        document.body.style.marginTop = '60px';
        document.body.appendChild(header);
    }, { role, feature, type });
    
    await new Promise(r => setTimeout(r, 1500)); // (500); // give time for render

    const filename = `${dir}/${feature}_${action}_${type}.png`;
    await page.screenshot({ path: filename, fullPage: true });
    console.log(`[${role.toUpperCase()}] Fitur: ${feature} | Test: ${type} | File: ${filename}`);
    
    // Remove header HTML
    await page.evaluate(() => {
        const header = document.getElementById('qa-test-header');
        if (header) {
            header.remove();
            document.body.style.marginTop = '0px';
        }
    });
}

async function runTest(role, email) {
    console.log(`\n=== Memulai Testing QA Blackbox untuk Role: ${role} ===`);
    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });
    
    try {
        // 1. Login (Positif)
        await page.goto('http://localhost:8088/login');
        await page.type('input[name="email"]', email);
        await page.type('input[name="password"]', 'password123');
        await Promise.all([
            page.waitForNavigation(),
            page.click('button[type="submit"]')
        ]);
        await takeScreenshot(page, role, 'Login', 'Berhasil', 'Positif');

        // Navigasi ke menu berdasarkan role
        const menus = await page.$$eval('.menu-link', links => links.map(a => ({ text: a.innerText.trim(), href: a.href })));
        
        for (const menu of menus) {
            // Hindari menu CRUD (Master) dan Logout
            if (menu.href.includes('javascript:')) continue;
            if (menu.text.includes('Master') || menu.text.includes('User') || menu.text.includes('Logout')) {
                continue;
            }

            console.log(`\nMenguji Menu: ${menu.text}`);
            await page.goto(menu.href);
            await new Promise(r => setTimeout(r, 1500)); // (1500); // Tunggu render
            await takeScreenshot(page, role, menu.text, 'Akses_Menu', 'Positif');

            // Coba klik tombol "Lihat Detail" atau "View" jika ada data (misal di Task / Content / Reimbursment)
            const detailLinks = await page.$$('a.btn-outline-primary, a.btn-primary, a[href*="show"]');
            if (detailLinks.length > 0) {
                console.log(`Menemukan data detail di menu ${menu.text}, mencoba membuka...`);
                // Ambil href dari elemen pertama
                const href = await page.evaluate(el => el.href, detailLinks[0]);
                if (href && !href.includes('javascript:')) {
                    await page.goto(href);
                    await new Promise(r => setTimeout(r, 1500)); // (1500);
                    await takeScreenshot(page, role, menu.text, 'Buka_Detail_Data', 'Positif');
                    // Kembali ke halaman sebelumnya
                    await page.goBack();
                    await new Promise(r => setTimeout(r, 1500)); // (1000);
                }
            }
        }

    } catch (e) {
        console.error(`Error saat testing role ${role}:`, e);
    } finally {
        await browser.close();
        console.log(`=== Selesai Testing Role: ${role} ===\n`);
    }
}

async function main() {
    const roles = [
        { name: 'Admin', email: 'admin@example.com' },
        { name: 'Creative_Director', email: 'creative@example.com' },
        { name: 'Finance', email: 'finance@example.com' },
        { name: 'Tim_Internal', email: 'budi@example.com' },
        { name: 'Sosmed_Spesialis', email: 'sosmed@example.com' }
    ];

    for (const role of roles) {
        await runTest(role.name, role.email);
    }
}

main();
