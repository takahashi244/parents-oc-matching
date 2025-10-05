import fs from 'fs';
import path from 'path';

const UNIVERSITY_NAME = '尚美学園大学';
const SCHOOL_ID = 'SCH0065';
const PLACE = '埼玉県川越市 尚美学園大学キャンパス';
const START_TIME = '10:00';
const END_TIME = '15:00';

const htmlPath = 'tmp/shobi_oc2.html';
if (!fs.existsSync(htmlPath)) {
  console.error(`HTML source not found: ${htmlPath}`);
  process.exit(1);
}

const html = fs.readFileSync(htmlPath, 'utf8');
const eventRegex = /href="(\/examinee\/opencampus\/opencampus\/(\d{8})\/)"/g;
const events = [];
let match;
while ((match = eventRegex.exec(html)) !== null) {
  const urlPath = match[1];
  const dateStr = match[2];
  const year = dateStr.slice(0, 4);
  const month = dateStr.slice(4, 6);
  const day = dateStr.slice(6, 8);
  const isoDate = `${year}-${month}-${day}`;
  events.push({
    date: isoDate,
    url: `https://www.shobi-u.ac.jp${urlPath}`,
  });
}

if (events.length === 0) {
  console.error('No events found in HTML.');
  process.exit(1);
}

const departmentsCsvPath = 'database/seeders/data/departments.csv';
const ocCsvPath = 'database/seeders/data/oc_events.csv';
const docsOcCsvPath = 'docs/07_セットアップ／シード／計測ヘルパ／機能フラグ/oc_events.csv';

const departmentsCsv = fs.readFileSync(departmentsCsvPath, 'utf8').trim().split(/\r?\n/);
const deptHeaders = departmentsCsv[0].split(',');
const deptRows = departmentsCsv.slice(1).map(line => {
  const cols = [];
  let current = '';
  let inQuotes = false;
  for (let i = 0; i < line.length; i++) {
    const char = line[i];
    if (char === '"') {
      if (inQuotes && line[i + 1] === '"') {
        current += '"';
        i++;
      } else {
        inQuotes = !inQuotes;
      }
    } else if (char === ',' && !inQuotes) {
      cols.push(current);
      current = '';
    } else {
      current += char;
    }
  }
  cols.push(current);
  const record = {};
  deptHeaders.forEach((key, idx) => {
    record[key] = cols[idx] ?? '';
  });
  return record;
});

const targetDepartments = deptRows.filter(row => row.school_id === SCHOOL_ID);
if (targetDepartments.length === 0) {
  console.error('No departments found for school', SCHOOL_ID);
  process.exit(1);
}

const ocCsvLines = fs.readFileSync(ocCsvPath, 'utf8').trim().split(/\r?\n/);
const ocHeaders = ocCsvLines[0].split(',');
const outputLines = [...ocCsvLines];
let counter = ocCsvLines.length; // approximate unique numbering by appended rows

function escape(value) {
  if (/[",\n]/.test(value)) {
    return `"${value.replace(/"/g, '""')}"`;
  }
  return value;
}

for (const event of events) {
  for (const dept of targetDepartments) {
    const ocevId = `EV${String(counter).padStart(4, '0')}`;
    counter++;
    const row = [
      ocevId,
      dept.dept_id,
      event.date,
      START_TIME,
      END_TIME,
      PLACE,
      'onsite',
      event.url,
    ];
    outputLines.push(row.map(escape).join(','));
  }
}

const csvContent = outputLines.join('\n') + '\n';
fs.writeFileSync(ocCsvPath, csvContent, 'utf8');
if (fs.existsSync(docsOcCsvPath)) {
  fs.writeFileSync(docsOcCsvPath, csvContent, 'utf8');
}

console.log(`Added ${events.length} events for ${targetDepartments.length} departments of ${UNIVERSITY_NAME}.`);
