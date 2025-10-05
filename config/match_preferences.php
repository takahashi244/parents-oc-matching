<?php

return [
    'categories' => [
        'music' => [
            'label' => '音楽',
            'options' => [
                ['id' => 'jpop', 'label' => 'J-POP / 邦楽', 'tags' => ['music'], 'reason' => '最新のJ-POP制作環境で実践的に学べます。'],
                ['id' => 'live', 'label' => 'ライブ・演奏', 'tags' => ['music', 'media'], 'reason' => 'ライブ音響や収録設備に強みがあります。'],
                ['id' => 'dtm', 'label' => '作曲・DTM', 'tags' => ['music', 'programming'], 'reason' => '作曲ソフトとDTMカリキュラムが整っています。'],
            ],
        ],
        'anime_manga' => [
            'label' => 'アニメ・マンガ',
            'options' => [
                ['id' => 'character_design', 'label' => 'キャラクターデザイン', 'tags' => ['design_art'], 'reason' => 'キャラクターデザインの基礎から応用まで学べます。'],
                ['id' => 'animation', 'label' => 'アニメーション制作', 'tags' => ['media', 'design_art'], 'reason' => 'アニメ制作工程を一貫して体験できます。'],
                ['id' => 'story', 'label' => 'マンガ・ストーリー', 'tags' => ['design_art', 'history_social'], 'reason' => '物語づくりと演出力を鍛えられます。'],
            ],
        ],
        'media' => [
            'label' => '映像・メディア',
            'options' => [
                ['id' => 'video', 'label' => '映像制作', 'tags' => ['media', 'design_art'], 'reason' => '映像編集や配信スキルを深く学べます。'],
                ['id' => 'cg', 'label' => 'CG / VFX', 'tags' => ['media', 'programming'], 'reason' => 'CG・VFX制作環境が充実しています。'],
                ['id' => 'broadcast', 'label' => '放送・配信', 'tags' => ['media', 'english_global'], 'reason' => '放送・配信現場の実習が豊富です。'],
            ],
        ],
        'sports' => [
            'label' => 'スポーツ',
            'options' => [
                ['id' => 'player', 'label' => '競技者', 'tags' => ['sports'], 'reason' => '競技力向上のための指導が整っています。'],
                ['id' => 'trainer', 'label' => 'トレーナー', 'tags' => ['sports', 'medical'], 'reason' => 'スポーツ科学とケアが体系的に学べます。'],
                ['id' => 'management', 'label' => 'マネジメント', 'tags' => ['sports', 'business'], 'reason' => 'スポーツビジネス・運営を学べます。'],
            ],
        ],
        'medical' => [
            'label' => '医療・看護',
            'options' => [
                ['id' => 'nurse', 'label' => '看護師', 'tags' => ['medical'], 'reason' => '臨地実習と国家試験対策が整っています。'],
                ['id' => 'rehab', 'label' => 'リハビリ', 'tags' => ['medical', 'science'], 'reason' => 'リハビリ・ケアの専門教育が受けられます。'],
                ['id' => 'clinical', 'label' => '臨床検査', 'tags' => ['medical', 'science'], 'reason' => '医療検査の臨床スキルを身につけられます。'],
            ],
        ],
        'international' => [
            'label' => '国際・語学',
            'options' => [
                ['id' => 'study_abroad', 'label' => '留学・海外進学', 'tags' => ['english_global'], 'reason' => '海外留学支援と英語運用力を鍛えられます。'],
                ['id' => 'diplomacy', 'label' => '国際関係', 'tags' => ['english_global', 'history_social'], 'reason' => '国際関係やグローバル課題を学べます。'],
                ['id' => 'languages', 'label' => '多言語', 'tags' => ['english_global'], 'reason' => '多言語習得と国際交流の機会が豊富です。'],
            ],
        ],
        'business' => [
            'label' => 'ビジネス・起業',
            'options' => [
                ['id' => 'entrepreneur', 'label' => 'スタートアップ', 'tags' => ['business', 'math_data'], 'reason' => '起業プログラムと実践演習が用意されています。'],
                ['id' => 'marketing', 'label' => 'マーケティング', 'tags' => ['business', 'media'], 'reason' => 'マーケティングとデータ分析を体系的に学べます。'],
                ['id' => 'finance', 'label' => '金融・会計', 'tags' => ['business', 'math_data'], 'reason' => '金融・会計の専門科目が充実しています。'],
            ],
        ],
        'technology' => [
            'label' => 'テクノロジー',
            'options' => [
                ['id' => 'ai', 'label' => 'AI / データ', 'tags' => ['programming', 'math_data'], 'reason' => 'AIとデータ分析を実践的に学べます。'],
                ['id' => 'robotics', 'label' => 'ロボティクス', 'tags' => ['programming', 'science'], 'reason' => 'ロボット開発の実験設備が整っています。'],
                ['id' => 'security', 'label' => 'セキュリティ', 'tags' => ['programming'], 'reason' => 'サイバーセキュリティの専門カリキュラムがあります。'],
            ],
        ],
        'education' => [
            'label' => '教育・心理',
            'options' => [
                ['id' => 'teacher', 'label' => '教師', 'tags' => ['edu_psych'], 'reason' => '教員免許取得と実習が充実しています。'],
                ['id' => 'psychology', 'label' => '心理支援', 'tags' => ['edu_psych', 'history_social'], 'reason' => '心理支援とカウンセリングを学べます。'],
                ['id' => 'childcare', 'label' => '幼児教育', 'tags' => ['edu_psych'], 'reason' => '幼児教育と保育実習の環境が整っています。'],
            ],
        ],
        'science' => [
            'label' => '自然・科学',
            'options' => [
                ['id' => 'environment', 'label' => '環境・エコ', 'tags' => ['science', 'history_social'], 'reason' => '環境課題の研究とフィールドワークがあります。'],
                ['id' => 'space', 'label' => '宇宙・航空', 'tags' => ['science', 'programming'], 'reason' => '宇宙工学・航空学を専門的に学べます。'],
                ['id' => 'biology', 'label' => 'バイオ', 'tags' => ['science', 'medical'], 'reason' => 'バイオ研究と実験環境が整っています。'],
            ],
        ],
    ],

    'subjects' => [
        '国語' => [
            ['tag' => 'history_social', 'weight' => 4],
            ['tag' => 'edu_psych', 'weight' => 3],
        ],
        '数学' => [
            ['tag' => 'math_data', 'weight' => 6],
            ['tag' => 'programming', 'weight' => 4],
        ],
        '英語' => [
            ['tag' => 'english_global', 'weight' => 7],
        ],
        '理科' => [
            ['tag' => 'science', 'weight' => 6],
            ['tag' => 'medical', 'weight' => 3],
        ],
        '社会' => [
            ['tag' => 'history_social', 'weight' => 6],
            ['tag' => 'business', 'weight' => 3],
        ],
        '情報' => [
            ['tag' => 'programming', 'weight' => 6],
            ['tag' => 'media', 'weight' => 3],
        ],
        '芸術' => [
            ['tag' => 'design_art', 'weight' => 6],
            ['tag' => 'music', 'weight' => 3],
        ],
        '保健体育' => [
            ['tag' => 'sports', 'weight' => 6],
            ['tag' => 'medical', 'weight' => 2],
        ],
        '物理' => [
            ['tag' => 'science', 'weight' => 6],
            ['tag' => 'programming', 'weight' => 2],
        ],
        '化学' => [
            ['tag' => 'science', 'weight' => 6],
            ['tag' => 'medical', 'weight' => 4],
        ],
        '生物' => [
            ['tag' => 'science', 'weight' => 6],
            ['tag' => 'medical', 'weight' => 4],
        ],
        '地理' => [
            ['tag' => 'history_social', 'weight' => 6],
            ['tag' => 'english_global', 'weight' => 2],
        ],
        '歴史' => [
            ['tag' => 'history_social', 'weight' => 7],
        ],
        '公共' => [
            ['tag' => 'history_social', 'weight' => 5],
            ['tag' => 'business', 'weight' => 3],
        ],
    ],

    'hero_categories' => [
        'entrepreneur' => ['label' => '起業家', 'tags' => ['business', 'math_data'], 'reason' => '起業家に憧れるあなたへ、ビジネスを実践的に学べる環境です。'],
        'designer'     => ['label' => 'デザイナー', 'tags' => ['design_art', 'media'], 'reason' => 'デザイナーを目指す人に向けた制作設備が整っています。'],
        'engineer'     => ['label' => 'エンジニア', 'tags' => ['programming', 'math_data'], 'reason' => 'エンジニアとして役立つ開発環境と先端研究があります。'],
        'researcher'   => ['label' => '研究者', 'tags' => ['science'], 'reason' => '研究者の道に進むための研究室と実験機材が揃っています。'],
        'artist'       => ['label' => 'アーティスト', 'tags' => ['music', 'design_art'], 'reason' => '表現力を磨けるアトリエやスタジオが充実しています。'],
        'medical'      => ['label' => '医療従事者', 'tags' => ['medical', 'science'], 'reason' => '医療現場さながらの実習施設で学べます。'],
        'educator'     => ['label' => '教育者', 'tags' => ['edu_psych'], 'reason' => '教育実習や心理支援を通じて子どもに寄り添う力を磨けます。'],
        'creator'      => ['label' => 'クリエイター', 'tags' => ['media', 'programming'], 'reason' => 'クリエイティブ制作のための専門設備が揃っています。'],
        'journalist'   => ['label' => 'ジャーナリスト', 'tags' => ['english_global', 'history_social'], 'reason' => '社会課題を取材し発信するための学びがあります。'],
    ],

    'tag_reasons' => [
        'programming'    => 'AIやソフトウェア開発の学びが豊富です。',
        'math_data'      => 'データ分析や統計を活かした学びができます。',
        'english_global' => '国際交流と語学力を育てるカリキュラムがあります。',
        'media'          => '映像・配信の実践的スキルを磨けます。',
        'design_art'     => 'デザインとアートの創作環境が充実しています。',
        'game_cg'        => 'ゲーム・CG制作の専用カリキュラムがあります。',
        'music'          => '音楽制作やパフォーマンスの指導が充実しています。',
        'history_social' => '社会課題や歴史を深掘りする授業が揃っています。',
        'medical'        => '医療・福祉分野の専門教育が整っています。',
        'science'        => '理系実験や研究の環境が整っています。',
        'sports'         => 'スポーツ科学と指導の両面を学べます。',
        'business'       => 'ビジネス戦略や実践が学べます。',
        'edu_psych'      => '教育・心理支援の専門力を養えます。',
    ],

    'oc' => [
        'date_score' => [
            ['max_days' => 30, 'score' => 5],
            ['max_days' => 60, 'score' => 3],
            ['max_days' => 90, 'score' => 1],
        ],
        'same_prefecture_score' => 2,
        'online_score' => 2,
        'reason_templates' => [
            'default' => '直近:date に :format 開催。:place で参加しやすいイベントです。',
            'online'  => '直近:dateにオンライン開催予定。自宅から気軽に参加できます。',
        ],
    ],
];
