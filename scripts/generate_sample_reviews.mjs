import fs from 'fs';
import path from 'path';

const schoolsPath = 'database/seeders/data/schools.csv';
const departmentsPath = 'database/seeders/data/departments.csv';
const ocEventsPath = 'database/seeders/data/oc_events.csv';
const outputPath = 'database/seeders/data/reviews.csv';
const docsOutputPath = 'docs/07_セットアップ／シード／計測ヘルパ／機能フラグ/reviews.csv';

function parseCsv(filePath) {
  const content = fs.readFileSync(filePath, 'utf8').trim();
  const lines = content.split(/\r?\n/);
  const headers = lines[0].split(',');
  return lines.slice(1).map(line => {
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
    headers.forEach((key, idx) => {
      record[key] = cols[idx] ?? '';
    });
    return record;
  });
}

const schools = parseCsv(schoolsPath);
const departments = parseCsv(departmentsPath);
const ocEvents = parseCsv(ocEventsPath);

function normalizeName(name) {
  return name.replace(/（.*?）/g, '').replace(/\s+/g, '').trim();
}

const schoolByNormalized = new Map();
schools.forEach(s => {
  schoolByNormalized.set(normalizeName(s.school_name), s);
});
const departmentsBySchool = new Map();
departments.forEach(dept => {
  const list = departmentsBySchool.get(dept.school_id) ?? [];
  list.push(dept);
  departmentsBySchool.set(dept.school_id, list);
});

const ocByDept = new Map();
ocEvents.forEach(ev => {
  const list = ocByDept.get(ev.dept_id) ?? [];
  list.push(ev);
  ocByDept.set(ev.dept_id, list);
});

const samples = [
  {
    university: '東京大学',
    departmentKeyword: '法学部',
    eventDate: '2025-08-05',
    author: 'parent',
    rating: 5,
    pros: [
      '在学生による学部紹介が丁寧で、進路のイメージが具体化できた',
      'キャンパスツアーで教養学部と専門課程の違いを知ることができた',
      'オンライン配信もあり後から復習できる資料が豊富'
    ],
    cons: [
      '参加者が多く質問コーナーは抽選制だった',
      '駅から会場までの導線案内がもう少し分かりやすいと助かる'
    ],
    notes: '保護者として参加。模擬講義はハイレベルでしたが、資料が後日公開されるので復習しやすいです。子どもは学生相談ブースで履修の組み方を詳しく聞けました。'
  },
  {
    university: '早稲田大学',
    departmentKeyword: '人間科学部',
    eventDate: '2025-08-02',
    author: 'parent',
    rating: 4,
    pros: [
      '研究室公開で実際の実験設備を体験できた',
      '学生スタッフが地方出身者の生活面についてリアルな話をしてくれた'
    ],
    cons: [
      'オンライン個別相談は予約枠がすぐ埋まってしまった'
    ],
    notes: '事前予約必須。学部説明で入試方式ごとのポイントがまとまっており、次の模試対策にも活かせそうです。'
  },
  {
    university: '慶應義塾大学',
    departmentKeyword: '法学部 法律学科',
    eventDate: '2025-08-05',
    author: 'student',
    rating: 5,
    pros: [
      '在学生メンターと1対1で話す枠があり、履修やサークルの雰囲気を詳しく聞けた',
      'キャンパスの雰囲気を動画だけでなく実際に体感できた'
    ],
    cons: [
      '人気の模擬講義は当日受付で満席になっていた'
    ],
    notes: '三田キャンパスでの開催。学生生活の紹介が中心で、入学後の不安が解消できました。'
  },
  {
    university: '上智大学',
    departmentKeyword: '理工学部',
    eventDate: '2025-08-02',
    author: 'parent',
    rating: 4,
    pros: [
      '研究室見学が少人数で質問しやすかった',
      '英語による授業の比率や留学制度について具体的な説明があった'
    ],
    cons: [
      '昼食スペースが混雑していたので時間配分に注意が必要'
    ],
    notes: '理工学部志望の娘と参加。実験装置のデモを見て将来の学びがイメージできました。'
  },
  {
    university: '立教大学',
    departmentKeyword: '社会学部',
    eventDate: '2025-08-04',
    author: 'student',
    rating: 4,
    pros: [
      'ゼミの在学生との座談会でカリキュラムの選び方を教えてもらえた',
      'キャンパスツアーで図書館やラーニングスペースを見学できた'
    ],
    cons: [
      '池袋キャンパスは人が多く、個別相談の待ち時間が長かった'
    ],
    notes: '社会学部の雰囲気が自分に合っていると感じました。池袋と新座で内容が分かれているので、希望学部の日程を事前に確認しておくと良いです。'
  },
  {
    university: '明治大学',
    departmentKeyword: '理工学部 電気電子生命学科',
    eventDate: '2025-08-02',
    author: 'parent',
    rating: 5,
    pros: [
      '研究室公開で学生が自分の研究をプレゼンしてくれた',
      '体験授業でプログラミングの演習を保護者も見学できた'
    ],
    cons: [
      'キャンパスが坂の途中にあり、移動が少し大変'
    ],
    notes: '理系志望の息子と参加。実験系の設備が充実していて、学生の雰囲気も活発でした。'
  },
  {
    university: '中央大学',
    departmentKeyword: '経済学部 国際経済学科',
    eventDate: '2025-08-06',
    author: 'student',
    rating: 4,
    pros: [
      '個別相談で留学プログラムの話が聞けた',
      '模擬授業でケーススタディを体験できた'
    ],
    cons: [
      'シャトルバスの本数がもう少し多いと助かる'
    ],
    notes: 'グローバル志向のカリキュラムが魅力的でした。多摩キャンパスは自然が多く、勉強に集中できそうです。'
  },
  {
    university: '法政大学',
    departmentKeyword: 'スポーツ健康学部 スポーツ健康学科',
    eventDate: '2025-07-06',
    author: 'parent',
    rating: 4,
    pros: [
      '実習施設を見学でき、指導教員が研究内容を丁寧に説明してくれた',
      '卒業生の進路事例が展示されていてイメージがつかみやすい'
    ],
    cons: [
      '参加申込が複数日に分かれており、希望枠を確保するのに少し苦労した'
    ],
    notes: 'スポーツ系志望の子どもと参加。競技経験を活かした学び方について具体的なアドバイスをもらえました。'
  },
  {
    university: '横浜国立大学',
    departmentKeyword: '教育学部',
    eventDate: '2025-06-21',
    author: 'student',
    rating: 5,
    pros: [
      '模擬授業でアクティブラーニングを体験できた',
      '実習に同行した学生の体験談がとても参考になった'
    ],
    cons: [
      '来場型の定員が限られており、申し込み開始と同時に行動する必要がある'
    ],
    notes: '教育志望として参加。教員養成のサポート体制が手厚く、入学後のイメージが湧きました。'
  },
  {
    university: '埼玉大学',
    departmentKeyword: '工学部',
    eventDate: '2025-08-07',
    author: 'parent',
    rating: 4,
    pros: [
      '学部ごとのタイムテーブルが分かれており効率よく回れた',
      '研究室ツアーで学生が制作したロボットに触れられた'
    ],
    cons: [
      'オンライン配信と来場を両方見ると情報量が多くて整理が大変'
    ],
    notes: '理工系志望の娘と参加。教員との距離が近く、質問にも丁寧に答えていただけました。'
  },
  {
    university: '千葉大学',
    departmentKeyword: '法政経学部 法政経学科',
    eventDate: '2025-09-14',
    author: 'student',
    rating: 3,
    pros: [
      'キャンパスツアーで各学部の施設を紹介してくれた',
      'Webコンテンツで事前学習してから参加できる'
    ],
    cons: [
      '夏の来場型が少なく、秋まで待つ必要がある'
    ],
    notes: '秋のオープンデイ情報が事前に公開されているので計画を立てやすいです。'
  },
  {
    university: '尚美学園大学',
    departmentKeyword: '芸術情報学部 情報表現学科',
    eventDate: '2025-07-06',
    author: 'parent',
    rating: 5,
    pros: [
      'DTMスタジオや最新の音響設備を体験できた',
      '学生スタッフが制作した作品を見ながら説明してくれた'
    ],
    cons: [
      '川越駅からスクールバスで少し時間がかかる'
    ],
    notes: '音楽系志望の息子と参加。教員との個別相談で入試対策のアドバイスを頂けました。'
  },
  {
    university: '東京大学',
    departmentKeyword: '工学部 機械工学科',
    eventDate: '2025-08-06',
    author: 'student',
    rating: 5,
    pros: [
      '研究室ツアーで最先端ロボットのデモを見せてもらえた',
      '学生スタッフが進級時の専門選択について具体的に教えてくれた'
    ],
    cons: [
      '事前質問を用意しておかないと個別相談の時間が短く感じる'
    ],
    notes: '自分の研究テーマをイメージできるようになりました。教員との距離も近く、学びの環境が充実していると感じます。'
  },
  {
    university: '早稲田大学',
    departmentKeyword: '文化構想学部',
    eventDate: '2025-08-03',
    author: 'student',
    rating: 4,
    pros: [
      '在学生によるゼミ紹介でカリキュラムの柔軟性を知ることができた',
      '模擬講義が複数回実施され、興味のあるテーマを選べた'
    ],
    cons: [
      '人気プログラムは立ち見になるほど混雑していた'
    ],
    notes: '文系でも幅広い分野を学べる点が魅力的でした。入試情報の冊子が非常に充実しており、受験勉強の指針になります。'
  },
  {
    university: '慶應義塾大学',
    departmentKeyword: '商学部 商学科',
    eventDate: '2025-06-22',
    author: 'parent',
    rating: 4,
    pros: [
      '模擬授業でケーススタディを親子で体験できた',
      '就職支援の取り組みについて具体的なデータが提示された'
    ],
    cons: [
      '日吉キャンパスは坂が多く、移動に余裕を持つ必要がある'
    ],
    notes: '商学部志望の子どもと参加。OB・OGによる進路体験談が充実しており、将来像を描きやすくなりました。'
  },
  {
    university: '明治大学',
    departmentKeyword: '法学部',
    eventDate: '2025-08-06',
    author: 'student',
    rating: 4,
    pros: [
      '模擬裁判の企画で学びの雰囲気がよく伝わった',
      '個別相談で推薦入試のポイントを細かく教えてもらえた'
    ],
    cons: [
      '予約枠が時間ごとに分かれていて少し分かりにくい'
    ],
    notes: '駿河台キャンパスの施設が綺麗で、学習スペースも豊富でした。'
  },
  {
    university: '中央大学',
    departmentKeyword: '理工学部 電気電子情報通信工学科',
    eventDate: '2025-08-07',
    author: 'parent',
    rating: 4,
    pros: [
      '研究室公開で実際の研究成果を体験できた',
      '後楽園キャンパスのアクセスが良く、通学イメージを持ちやすい'
    ],
    cons: [
      '専門的な説明が多く、予習しておくと理解しやすい'
    ],
    notes: '技術系志望の子どもと参加。教員から入試対策の勉強法を具体的に教えていただけました。'
  },
  {
    university: '法政大学',
    departmentKeyword: '社会学部 社会政策科学科',
    eventDate: '2025-08-08',
    author: 'student',
    rating: 4,
    pros: [
      '社会問題を扱うゼミのプレゼンが印象的だった',
      '学生相談ブースで奨学金や留学制度の詳細を聞けた'
    ],
    cons: [
      '小金井キャンパスは広く、事前にマップを確認しておくと安心'
    ],
    notes: '社会問題に関心があり参加。授業内容が実社会に直結しているとの説明があり、進学意欲が高まりました。'
  },
  {
    university: '埼玉大学',
    departmentKeyword: '教育学部 学校教育教員養成課程',
    eventDate: '2025-08-06',
    author: 'student',
    rating: 4,
    pros: [
      '模擬授業で授業づくりのワークショップを体験できた',
      '教員志望の学生と直接話す機会があった'
    ],
    cons: [
      'オンライン視聴もあるため情報整理に時間がかかる'
    ],
    notes: '教師志望として参加。実習サポートの体制が手厚いと感じました。'
  },
  {
    university: '千葉大学',
    departmentKeyword: '理学部 数学・情報数理学科',
    eventDate: '2025-12-07',
    author: 'parent',
    rating: 4,
    pros: [
      '学部説明で研究テーマの広がりを知ることができた',
      '学生が制作したポスターの展示がありイメージが湧いた'
    ],
    cons: [
      '冬の開催で屋外移動が寒かったので防寒が必須'
    ],
    notes: '数学系志望の子どもと参加。教授陣が丁寧に相談に乗ってくれて安心しました。'
  },
  {
    university: '尚美学園大学',
    departmentKeyword: '総合政策学部 総合政策学科',
    eventDate: '2025-08-22',
    author: 'student',
    rating: 4,
    pros: [
      '少人数の模擬授業で地域課題のディスカッションに参加できた',
      '学生スタッフが履修やサークルについて親身に相談に乗ってくれた'
    ],
    cons: [
      'スクールバスは時間帯によって混雑する'
    ],
    notes: '政策系に興味があり参加。地域連携の取り組みが多く、実践的な学びができそうだと感じました。'
  }
];

function escape(value) {
  if (value === null || value === undefined) return '';
  const str = String(value);
  if (/[",\n]/.test(str)) {
    return `"${str.replace(/"/g, '""')}"`;
  }
  return str;
}

function findDepartment(spec) {
const school = schoolByNormalized.get(normalizeName(spec.university));
  if (!school) return null;
  const depts = departmentsBySchool.get(school.school_id) ?? [];
  return depts.find(dept => dept.dept_name.includes(spec.departmentKeyword)) || null;
}

function findEvent(deptId, date) {
  const list = ocByDept.get(deptId) ?? [];
  let event = null;
  if (date) {
    event = list.find(ev => ev.date === date);
  }
  if (!event && list.length > 0) {
    event = list[0];
  }
  return event;
}

const rows = [];
let counter = 1;
for (const sample of samples) {
  const dept = findDepartment(sample);
  if (!dept) {
    console.warn('Department not found for', sample.university, sample.departmentKeyword);
    continue;
  }
  const event = findEvent(dept.dept_id, sample.eventDate);
  if (!event) {
    console.warn('Event not found for', dept.dept_id, sample.eventDate);
    continue;
  }
  const revId = `RV${String(counter).padStart(4, '0')}`;
  counter++;
  rows.push({
    rev_id: revId,
    ocev_id: event.ocev_id,
    author_role: sample.author,
    rating: sample.rating,
    pros: JSON.stringify(sample.pros, null, 0),
    cons: JSON.stringify(sample.cons, null, 0),
    notes: sample.notes,
    is_published: 1,
    created_at: `${sample.eventDate} 12:00:00`,
    updated_at: `${sample.eventDate} 12:00:00`,
  });
}

if (rows.length === 0) {
  console.error('No reviews generated.');
  process.exit(1);
}

const headers = ['rev_id','ocev_id','author_role','rating','pros','cons','notes','is_published','created_at','updated_at'];
const lines = [headers.join(',')];
rows.forEach(row => {
  const line = headers.map(key => escape(row[key] ?? '')).join(',');
  lines.push(line);
});
const csv = lines.join('\n') + '\n';
fs.writeFileSync(outputPath, csv, 'utf8');
if (fs.existsSync(path.dirname(docsOutputPath))) {
  fs.writeFileSync(docsOutputPath, csv, 'utf8');
}

console.log(`Generated ${rows.length} sample reviews.`);
