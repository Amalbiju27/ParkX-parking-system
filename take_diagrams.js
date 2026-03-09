import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';

(async () => {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    await page.setViewport({ width: 1400, height: 2800 });

    const htmlPath = `file:///${path.resolve('diagrams.html').replace(/\\/g, '/')}`;
    await page.goto(htmlPath, { timeout: 60000 });

    // wait for mermaid to format
    await new Promise(r => setTimeout(r, 5000));

    const usecase = await page.$('#usecase');
    if (usecase) {
        await usecase.screenshot({ path: 'Parkx_FSD_Report/use_case.png' });
        console.log('use_case.png saved');
    }

    const activity = await page.$('#activity_booking');
    if (activity) {
        await activity.screenshot({ path: 'Parkx_FSD_Report/activity_booking.png' });
        console.log('activity_booking.png saved');
    }

    const er = await page.$('#er_diagram');
    if (er) {
        await er.screenshot({ path: 'Parkx_FSD_Report/er_diagram.png' });
        console.log('er_diagram.png saved');
    }

    const class_diag = await page.$('#class_diagram');
    if (class_diag) {
        await class_diag.screenshot({ path: 'Parkx_FSD_Report/class_diagram.png' });
        console.log('class_diagram.png saved');
    }

    const arch = await page.$('#architecture');
    if (arch) {
        await arch.screenshot({ path: 'Parkx_FSD_Report/architecture.png' });
        console.log('architecture.png saved');
    }

    const seq = await page.$('#sequence');
    if (seq) {
        await seq.screenshot({ path: 'Parkx_FSD_Report/sequence.png' });
        console.log('sequence.png saved');
    }

    const pl = await page.$('#pipeline');
    if (pl) {
        await pl.screenshot({ path: 'Parkx_FSD_Report/pipeline.png' });
        console.log('pipeline.png saved');
    }

    await browser.close();
})();
