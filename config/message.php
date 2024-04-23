<?php

 

// +----------------------------------------------------------------------

// | 消息模板

// +----------------------------------------------------------------------

 

return [

    // 系统消息模板

    'template'  => [

        1 => [

            'title'       => '{from_user}が新しい『お知らせ』を投稿しました。ご確認ください',

            'content' => '新しいお知らせがあります: {title}。',

            'link' => '<a class="link-a" data-href="/note/index/view/id/{action_id}">詳細を見る</a>',

        ],

        21 => [

            'title'       => '{from_user}が『{title}申請』を提出しました。ご審査をお願いします',

            'content' => '新しい『{title}審査』があります。審査を行ってください。',

            'link' => '<a class="link-a" data-href="/oa/approve/view/id/{action_id}">審査へ</a>',

        ],

        22 => [

            'title'       => 'あなたの『{title}申請』が承認されました',

            'content' => '{create_time}に提出された『{title}申請』が{date}に承認されました。',

            'link' => '<a class="link-a" data-href="/oa/approve/view/id/{action_id}">詳細を見る</a>',

        ],

        23 => [

            'title'       => 'あなたの『{title}申請』は却下されました',

            'content' => '{create_time}に提出された『{title}申請』が{date}に却下されました。',

            'link' => '<a class="link-a" data-href="/oa/approve/view/id/{action_id}">詳細を見る</a>',

        ],

        31 => [

            'title'       => '{from_user}が新しい『経費申請』を提出しました。ご審査をお願いします',

            'content' => '新しい『経費審査』があります。審査を行ってください。',

            'link' => '<a class="link-a" data-href="/finance/expense/view/id/{action_id}">審査へ</a>',

        ],

        32 => [

            'title'       => 'あなたの『経費申請』が承認されました',

            'content' => '{create_time}に提出された『経費申請』が{date}に承認されました。',

            'link' => '<a class="link-a" data-href="/finance/expense/view/id/{action_id}">詳細を見る</a>',

        ],

        33 => [

            'title'       => 'あなたの『経費申請』は却下されました',

            'content' => '{create_time}に提出された『経費申請』が{date}に却下されました。',

            'link' => '<a class="link-a" data-href="/finance/expense/view/id/{action_id}">詳細を見る</a>',

        ],

        34 => [

            'title'       => 'あなたの『経費申請』は支払い済みです',

            'content' => '{create_time}に提出された『経費申請』は支払い済みです。お支払いの確認をお願いします。',

            'link' => '<a class="link-a" data-href="/finance/expense/view/id/{action_id}">詳細を見る</a>',

        ],

        41 => [

            'title'       => '{from_user}が新しい『請求書申請』を提出しました。ご審査をお願いします',

            'content' => '新しい『請求書審査』があります。審査を行ってください。',

            'link' => '<a class="link-a" data-href="/finance/invoice/view/id/{action_id}">審査へ</a>',

        ],

        42 => [

            'title'       => 'あなたの『請求書申請』が承認されました',

            'content' => '{create_time}に提出された『請求書申請』が{date}に承認されました。',

            'link' => '<a class="link-a" data-href="/finance/invoice/view/id/{action_id}">詳細を見る</a>',

        ],

        43 => [

            'title'       => 'あなたの『請求書申請』は却下されました',

            'content' => '{create_time}に提出された『請求書申請』が{date}に却下されました。',

            'link' => '<a class="link-a" data-href="/finance/invoice/view/id/{action_id}">詳細を見る</a>',

        ],

        51 => [

            'title'       => '{from_user}が新しい『契約審査』を提出しました。ご審査をお願いします',

            'content' => '新しい『契約審査』があります。審査を行ってください。',

            'link' => '<a class="link-a" data-href="/contract/index/view/id/{action_id}">審査へ</a>',

        ],

        52 => [

            'title'       => 'あなたの『契約審査』が承認されました',

            'content' => '{create_time}に提出された『契約審査』が{date}に承認されました。',

            'link' => '<a class="link-a" data-href="/contract/index/view/id/{action_id}">詳細を見る</a>',

        ],

        53 => [

            'title'       => 'あなたの『契約審査』は却下されました',

            'content' => '{create_time}に提出された『契約審査』が{date}に却下されました。',

            'link' => '<a class="link-a" data-href="/contract/index/view/id/{action_id}">詳細を見る</a>',

        ],

    ]

];