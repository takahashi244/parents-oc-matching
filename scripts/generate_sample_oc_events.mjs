import fs from 'fs';
import path from 'path';

function parseCsv(filePath) {
  const content = fs.readFileSync(filePath, 'utf8').trim();
  const [headerLine, ...lines] = content.split(/\r?\n/);
  const headers = headerLine.split(',');
  return lines.map(line => {
    const values = [];
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
        values.push(current);
        current = '';
      } else {
        current += char;
      }
    }
    values.push(current);
    const record = {};
    headers.forEach((key, idx) => {
      record[key] = values[idx] ?? '';
    });
    return record;
  });
}

const schoolsPath = path.resolve('database/seeders/data/schools.csv');
const departmentsPath = path.resolve('database/seeders/data/departments.csv');
const schools = parseCsv(schoolsPath);
const departments = parseCsv(departmentsPath);

const schoolById = new Map(schools.map(s => [s.school_id, s]));

const events = [];
const formats = ['onsite', 'online', 'hybrid'];
let baseDate = new Date('2025-10-01');
const incrementDays = 6;

const maxEvents = 40;
for (let i = 0; i < departments.length && events.length < maxEvents; i++) {
  const dept = departments[i];
  const school = schoolById.get(dept.school_id);
  if (!school) continue;

  const date = new Date(baseDate.getTime() + events.length * incrementDays * 24 * 60 * 60 * 1000);
  const yyyy = date.getFullYear();
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const dd = String(date.getDate()).padStart(2, '0');
  const format = formats[events.length % formats.length];
  const place = `${school.prefecture}${school.school_name}キャンパス`;

  events.push({
    ocev_id: `EV${String(events.length + 1).padStart(4, '0')}`,
    dept_id: dept.dept_id,
    date: `${yyyy}-${mm}-${dd}`,
    start_time: '10:00',
    end_time: '13:00',
    place,
    format,
    reservation_url: `https://example.com/oc/${dept.dept_id.toLowerCase()}`,
  });
}

const headers = ['ocev_id', 'dept_id', 'date', 'start_time', 'end_time', 'place', 'format', 'reservation_url'];
const lines = [headers.join(',')];
for (const ev of events) {
  const line = headers.map(key => {
    const value = ev[key] ?? '';
    return /[",\n]/.test(value)
      ? `"${value.replace(/"/g, '""')}"`
      : value;
  }).join(',');
  lines.push(line);
}

fs.writeFileSync(path.resolve('database/seeders/data/oc_events.csv'), lines.join('\n') + '\n', 'utf8');
console.log(`Generated ${events.length} OC events.`);
