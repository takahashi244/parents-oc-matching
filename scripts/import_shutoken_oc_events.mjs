import fs from 'fs';
import path from 'path';

const eventsSource = [
  {
    university: '東京大学',
    campus: 'オンライン',
    date_start: '2025-08-05',
    date_end: '2025-08-06',
    modality: 'オンライン',
    notes: 'オープンキャンパス2025（オンライン開催）',
    url: 'https://www.u-tokyo.ac.jp/ja/admissions/undergraduate/d04_02.html'
  },
  {
    university: '早稲田大学',
    campus: '早稲田/戸山/西早稲田/TWIns',
    date_start: '2025-08-02',
    date_end: '2025-08-03',
    modality: '来場',
    notes: '本学主催オープンキャンパス。8/23に人間科学部オンライン個別相談会あり',
    url: 'https://www.waseda.jp/top/news/112088'
  },
  {
    university: '慶應義塾大学（講義編）',
    campus: '日吉キャンパス',
    date_start: '2025-06-22',
    date_end: '2025-06-22',
    modality: '来場',
    notes: '学部説明・模擬講義中心（講義編）',
    url: 'https://www.keio.ac.jp/ja/admissions/oc-1/'
  },
  {
    university: '慶應義塾大学（学生生活編）',
    campus: '三田キャンパス',
    date_start: '2025-08-05',
    date_end: '2025-08-06',
    modality: '来場',
    notes: '在学生との懇談中心（学生生活編）',
    url: 'https://www.keio.ac.jp/ja/admissions/oc-2/'
  },
  {
    university: '東京科学大学（理工学系）',
    campus: '大岡山キャンパス',
    date_start: '2025-08-06',
    date_end: '2025-08-06',
    modality: '来場（一部オンライン配信）',
    notes: '理工学系オープンキャンパス2025',
    url: 'https://www.isct.ac.jp/ja/news/rpida5lj8wjh'
  },
  {
    university: '東京科学大学（医歯学系）',
    campus: '湯島キャンパス',
    date_start: '2025-07-31',
    date_end: '2025-08-01',
    modality: '来場',
    notes: '医歯学系オープンキャンパス2025',
    url: 'https://www.isct.ac.jp/ja/news/rpida5lj8wjh'
  },
  {
    university: '上智大学',
    campus: '四谷キャンパス',
    date_start: '2025-08-02',
    date_end: '2025-08-03',
    modality: '来場（配信なし）',
    notes: 'SOPHIA OPEN CAMPUS 2025（理工は2日間、他学部は日程別）',
    url: 'https://adm.sophia.ac.jp/jpn/sophia-open-campus-2025/'
  },
  {
    university: '立教大学',
    campus: '池袋/新座',
    date_start: '2025-08-02',
    date_end: '2025-08-08',
    modality: '来場',
    notes: '池袋：8/2,4,5／新座：8/7,8',
    url: 'https://www.rikkyo.ac.jp/admissions/visit/opencampus/?2403='
  },
  {
    university: '明治大学（駿河台）',
    campus: '駿河台キャンパス',
    date_start: '2025-08-06',
    date_end: '2025-08-07',
    modality: '来場（予約必須）',
    notes: '法・商・政経・文・経営・情報コミュニケーション・農（一部）',
    url: 'https://www.meiji.ac.jp/exam/event/opencampus/'
  },
  {
    university: '明治大学（生田）',
    campus: '生田キャンパス',
    date_start: '2025-08-02',
    date_end: '2025-08-03',
    modality: '来場',
    notes: '理工・農・総合数理など',
    url: 'https://www.meiji.ac.jp/exam/event/opencampus/'
  },
  {
    university: '中央大学（法/国際情報）',
    campus: '茗荷谷/市ヶ谷田町',
    date_start: '2025-08-02',
    date_end: '2025-08-03',
    modality: '来場（事前申込）',
    notes: '法学部（茗荷谷）、国際情報学部（市ヶ谷田町）',
    url: 'https://www.chuo-u.ac.jp/admission/connect/topics/2025/04/79744/'
  },
  {
    university: '中央大学（他学部）',
    campus: '多摩/後楽園',
    date_start: '2025-08-06',
    date_end: '2025-08-07',
    modality: '来場（事前申込）',
    notes: '多摩（経・商・文・総政・国際経営）／後楽園（基幹・社会・先進理工）',
    url: 'https://www.chuo-u.ac.jp/admission/connect/topics/2025/04/79744/'
  },
  {
    university: '法政大学',
    campus: '市ヶ谷/多摩/小金井',
    date_start: '',
    date_end: '',
    modality: '来場（事前予約制）',
    notes: '2025年度は3キャンパスで実施（詳細は学部別ページ参照）',
    url: 'https://nyushi.hosei.ac.jp/event/oc/'
  },
  {
    university: '横浜国立大学',
    campus: '常盤台キャンパス',
    date_start: '2025-06-21',
    date_end: '2025-06-22',
    modality: '来場＋オンライン（通年配信あり）',
    notes: '来場型は6/21-22で終了、オンライン型は6-11月に開催',
    url: 'https://www.ynu.ac.jp/exam/ynu/opencampus.html'
  },
  {
    university: '埼玉大学',
    campus: '大久保キャンパス',
    date_start: '2025-08-06',
    date_end: '2025-08-08',
    modality: '来場（一部ライブ/オンデマンド）',
    notes: '6日：教養・経済／7日：理・工／8日：教育',
    url: 'https://www.saitama-u.ac.jp/entrance/event/open/'
  },
  {
    university: '千葉大学',
    campus: '各キャンパス',
    date_start: '',
    date_end: '',
    modality: 'Web＋ガイドツアー（夏OCは実施なし）',
    notes: '秋に「千葉大学オープンデイ」開催、通年でWeb OC',
    url: 'https://www.chiba-u.ac.jp/admissions/event/opencampus.html'
  },
  {
    university: '東京理科大学',
    campus: '神楽坂/葛飾/野田',
    date_start: '',
    date_end: '',
    modality: '来場（要確認）',
    notes: '詳細・日程は公式ページ参照',
    url: 'https://www.tus.ac.jp/admissions/university/visittus/opencampus/'
  }
];

function loadCsv(filePath) {
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

const schools = loadCsv('database/seeders/data/schools.csv');
const departments = loadCsv('database/seeders/data/departments.csv');

const departmentsBySchool = new Map();
departments.forEach(dept => {
  const list = departmentsBySchool.get(dept.school_id) ?? [];
  list.push(dept);
  departmentsBySchool.set(dept.school_id, list);
});

function normalizeName(name) {
  return name.replace(/（.*?）/g, '').trim();
}

function selectSchools(universityName) {
  const base = normalizeName(universityName);
  return schools.filter(s => s.school_name.replace(/\s+/g, '') === base || s.school_name.includes(base));
}

function resolveFormat(modality) {
  if (!modality) return 'onsite';
  if (modality.includes('オンライン') && modality.includes('来場')) return 'hybrid';
  if (modality.includes('オンライン') || modality.includes('Web')) return 'online';
  return 'onsite';
}

function resolveDate(event) {
  if (event.date_start) return event.date_start;
  if (event.date_end) return event.date_end;
  return '2025-08-01';
}

const generated = [];
let counter = 1;

for (const event of eventsSource) {
  const matchedSchools = selectSchools(event.university);
  if (matchedSchools.length === 0) {
    console.warn(`No school matched for ${event.university}`);
    continue;
  }
  const format = resolveFormat(event.modality);
  const date = resolveDate(event);
  matchedSchools.forEach(school => {
    const deptList = departmentsBySchool.get(school.school_id) ?? [];
    deptList.forEach(dept => {
      generated.push({
        ocev_id: `EV${String(counter).padStart(4, '0')}`,
        dept_id: dept.dept_id,
        date,
        start_time: '10:00',
        end_time: '13:00',
        place: `${event.campus_or_location}`,
        format,
        reservation_url: event.url,
      });
      counter++;
    });
  });
}

const headers = ['ocev_id', 'dept_id', 'date', 'start_time', 'end_time', 'place', 'format', 'reservation_url'];
const lines = [headers.join(',')];
for (const row of generated) {
  const line = headers.map(key => {
    const value = row[key] ?? '';
    return /[",\n]/.test(value) ? `"${value.replace(/"/g, '""')}"` : value;
  }).join(',');
  lines.push(line);
}

fs.writeFileSync('database/seeders/data/oc_events.csv', lines.join('\n') + '\n', 'utf8');
fs.writeFileSync('docs/07_セットアップ／シード／計測ヘルパ／機能フラグ/oc_events.csv', lines.join('\n') + '\n', 'utf8');
console.log(`Generated ${generated.length} OC events for ${eventsSource.length} base entries.`);
