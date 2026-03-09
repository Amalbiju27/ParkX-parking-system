import mammoth from 'mammoth';
import fs from 'fs';

mammoth.extractRawText({ path: "Mantra_FSD_Report__6_ (1).docx" })
    .then(function (result) {
        const text = result.value;
        fs.writeFileSync('temp_mantra.txt', text);
        console.log("Extraction complete. Output written to temp_mantra.txt");
    })
    .catch(console.error);
