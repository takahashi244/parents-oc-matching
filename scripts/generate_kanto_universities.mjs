import fs from 'fs';
import path from 'path';
import xlsx from 'xlsx';

const KANTO_PREFS = new Set(['東京都', '神奈川県', '千葉県', '埼玉県', '茨城県', '栃木県', '群馬県']);

const SOURCE_FILES = [
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_01.xlsx', type: 'national' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_02.xlsx', type: 'public' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-1.xlsx', type: 'private' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-2.xlsx', type: 'private' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-3.xlsx', type: 'private' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-4.xlsx', type: 'private' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-5.xlsx', type: 'private' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-6.xlsx', type: 'private' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-7.xlsx', type: 'private' },
  { file: 'tmp/mext/20250625-mxt_daigakuc01-000043215_03-8.xlsx', type: 'private' },
];

const PREF_REGEX = /(北海道|青森県|岩手県|宮城県|秋田県|山形県|福島県|茨城県|栃木県|群馬県|埼玉県|千葉県|東京都|神奈川県|新潟県|富山県|石川県|福井県|山梨県|長野県|岐阜県|静岡県|愛知県|三重県|滋賀県|京都府|大阪府|兵庫県|奈良県|和歌山県|鳥取県|島根県|岡山県|広島県|山口県|徳島県|香川県|愛媛県|高知県|福岡県|佐賀県|長崎県|熊本県|大分県|宮崎県|鹿児島県|沖縄県)/;

const TAG_RULES = [
  { regex: /工学|理工|機械|電気|電子|情報|コンピュータ|システム|計算|建築|土木|都市|材料|応用化学|化学工学|航空|宇宙|ロボット|データサイエンス|数理工/, tags: ['programming', 'science'] },
  { regex: /理学|サイエンス|数理|数学|物理|化学|地球|生命理学|基礎科学|自然|環境理工|基幹理工|地球惑星|応用理工/, tags: ['science'] },
  { regex: /医学|医療|看護|保健|健康|薬学|歯学|獣医|リハビリ|臨床|医科学|医療保健/, tags: ['medical', 'science'] },
  { regex: /農|生命|生物|応用生物|獣医|水産|海洋|園芸|食品|栄養|バイオ|環境資源|農芸|農業|林/, tags: ['science'] },
  { regex: /文学|人文|文化|語学|外国語|言語|心理|社会|人間|教養|史学|哲学|日本語|英米|イスラム|社会学|社会情報/, tags: ['history_social'] },
  { regex: /外国語|国際|グローバル|英語|語学|通訳|国際関係|国際キャリア/, tags: ['english_global'] },
  { regex: /教育|教師|こども|児童|保育|幼児|心理|福祉|総合人間|発達|スクール|教育学|教職|こども学|人間教育/, tags: ['edu_psych', 'history_social'] },
  { regex: /法学|法科|政治|公共政策|法律|政策|行政|法務|国際法務/, tags: ['history_social', 'business'] },
  { regex: /経済|経営|商学|ビジネス|会計|金融|マーケティング|マネジメント|ホスピタリティ|観光|物流|経済学|経営学|商業|国際商/, tags: ['business', 'math_data'] },
  { regex: /社会|総合政策|現代社会|コミュニティ|人間科学|社会福祉|地域|社会デザイン|社会情報/, tags: ['history_social'] },
  { regex: /情報|データ|AI|ICT|メディア|ネットワーク|コンピュータ|サイバー|情報科学|情報工/, tags: ['programming', 'math_data'] },
  { regex: /デザイン|芸術|美術|造形|映像|演劇|舞台|音楽|建築デザイン|クリエイティブ|マンガ|アニメ/, tags: ['design_art'] },
  { regex: /音楽|ミュージック|声楽|演奏|管弦|作曲|ジャズ/, tags: ['music'] },
  { regex: /体育|スポーツ|健康スポーツ|武道|フィットネス|スポーツ科学/, tags: ['sports'] },
  { regex: /数理|統計|データサイエンス|データ科学/, tags: ['math_data'] },
];

function cleanValue(value) {
  if (typeof value !== 'string') return '';
  return value.replace(/^[0-9]+:/, '').replace(/\s+/g, ' ').trim();
}

function extractPrefecture(rows) {
  const limit = Math.min(rows.length, 40);
  for (let i = 0; i < limit; i++) {
    const row = rows[i];
    if (!Array.isArray(row)) continue;
    for (const cell of row) {
      if (typeof cell === 'string') {
        const match = cell.match(PREF_REGEX);
        if (match) return match[1];
      }
    }
  }
  return null;
}

function extractDepartments(rows, defaultPrefecture) {
  const departments = [];
  const startIndex = rows.findIndex(row => Array.isArray(row) && cleanValue(row[1]) === '学部');
  if (startIndex === -1) return departments;

  for (let i = startIndex + 2; i < rows.length; i++) {
    const row = rows[i];
    if (!Array.isArray(row)) continue;
    const rawFaculty = cleanValue(row[1]);
    if (!rawFaculty) continue;
    if (/^(合計|通信教育部|学部沿革|研究科|大学院)/.test(rawFaculty)) {
      break;
    }

    const faculty = rawFaculty;
    const department = cleanValue(row[3]);
    if (!faculty && !department) continue;

    const pref = cleanValue(row[6]) || defaultPrefecture || '';
    departments.push({ faculty, department, prefecture: pref });
  }
  return departments;
}

function determineTags(faculty, department) {
  const target = (faculty + ' ' + department).replace(/\s+/g, '');
  const tags = new Set();
  for (const rule of TAG_RULES) {
    if (rule.regex.test(target)) {
      rule.tags.forEach(tag => tags.add(tag));
    }
  }
  if (tags.size === 0 && /芸術|美術|デザイン|音楽/.test(target)) {
    tags.add('design_art');
  }
  if (tags.size === 0) {
    tags.add('history_social');
  }
  return Array.from(tags);
}

const schools = [];
const departments = [];
const seenSchools = new Set();

for (const source of SOURCE_FILES) {
  const absPath = path.resolve(source.file);
  if (!fs.existsSync(absPath)) {
    console.warn(`Missing source file: ${absPath}`);
    continue;
  }
  const workbook = xlsx.readFile(absPath, { cellDates: false });
  workbook.SheetNames.forEach(sheetName => {
    if (!sheetName || sheetName.includes('索引') || sheetName.includes('統計')) {
      return;
    }

    const sheet = workbook.Sheets[sheetName];
    if (!sheet) return;

    const rows = xlsx.utils.sheet_to_json(sheet, { header: 1, blankrows: false });
    const prefecture = extractPrefecture(rows);
    if (!prefecture || !KANTO_PREFS.has(prefecture)) {
      return;
    }

    const schoolKey = `${source.type}:${sheetName}`;
    if (seenSchools.has(schoolKey)) {
      return;
    }

    const schoolId = `SCH${String(schools.length + 1).padStart(4, '0')}`;
    schools.push({
      school_id: schoolId,
      school_name: sheetName.trim(),
      school_type: source.type,
      prefecture,
    });
    seenSchools.add(schoolKey);

    const deptList = extractDepartments(rows, prefecture);
    deptList.forEach(item => {
      const deptName = item.department ? `${item.faculty} ${item.department}` : item.faculty;
      const tags = determineTags(item.faculty, item.department);
      departments.push({
        dept_id: `DEP${String(departments.length + 1).padStart(4, '0')}`,
        school_id: schoolId,
        dept_name: deptName,
        tags,
      });
    });
  });
}

function toCsv(rows, headers) {
  const escape = value => {
    const str = String(value ?? '');
    if (/[",\n]/.test(str)) {
      return `"${str.replace(/"/g, '""')}"`;
    }
    return str;
  };
  const lines = [headers.map(escape).join(',')];
  rows.forEach(row => {
    const line = headers.map(key => {
      const value = key === 'tags' && Array.isArray(row[key])
        ? row[key].join(';')
        : row[key];
      return escape(value);
    }).join(',');
    lines.push(line);
  });
  return lines.join('\n') + '\n';
}

const schoolsCsv = toCsv(schools, ['school_id', 'school_name', 'school_type', 'prefecture']);
const departmentsCsv = toCsv(departments, ['dept_id', 'school_id', 'dept_name', 'tags']);

fs.writeFileSync(path.resolve('database/seeders/data/schools.csv'), schoolsCsv, 'utf8');
fs.writeFileSync(path.resolve('database/seeders/data/departments.csv'), departmentsCsv, 'utf8');

console.log(`Generated ${schools.length} schools and ${departments.length} departments.`);
