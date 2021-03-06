<?php
$username = $comment['User']['username'];
$avatarUrl = $members->imageUrl($comment['User']['image']);
$createdDate = $comment['SentenceComment']['created'];
$modifiedDate = $comment['SentenceComment']['modified'];
$commentId = $comment['SentenceComment']['id'];
$authorId = $comment['SentenceComment']['user_id'];
$commentText = $comment['SentenceComment']['text'];
$commentHidden = $comment['SentenceComment']['hidden'];
$sentence = null;
$sentenceOwnerLink = null;
if (isset($comment['Sentence'])) {
    $sentence = $comment['Sentence'];
}
if ($sentence && isset($sentence['User']['username'])) {
    $sentenceOwner = $sentence['User']['username'];
    $sentenceOwnerLink = $html->link(
        $sentenceOwner,
        array(
            'controller' => 'user',
            'action' => 'profile',
            $sentenceOwner
        )
    );
}
$sentenceId = $comment['SentenceComment']['sentence_id'];
$sentenceLink = $html->link(
    '#'.$sentenceId,
    array(
        'controller' => 'sentences',
        'action' => 'show',
        $sentenceId
    )
);
$sentenceText = '<em>'.__('sentence deleted', true).'</em>';
if (isset($sentence['text'])) {
    $sentenceText = $sentence['text'];
}
$sentenceLang = $sentence['lang'];
$sentenceOwner = null;
if (!empty($sentence['User'])) {
    $sentenceOwner = $sentence['User']['username'];
}
$langDir = LanguagesLib::getLanguageDirection($sentenceLang);

$editUrl = $html->url(array(
    'controller' => 'sentence_comments',
    'action' => 'edit',
    $commentId
));
$sentenceUrl = $html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId
));
$replyUrl = $html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId.'#comment-'.$commentId
));
if (empty($modifiedDate) || $createdDate == $modifiedDate) {
    $dateLabel = $date->ago($createdDate);
    $fullDateLabel = $createdDate;
} else {
    $dateLabel = format(
        __('{createdDate}, edited {modifiedDate}', true),
        array(
            'createdDate' => $date->ago($createdDate),
            'modifiedDate' => $date->ago($modifiedDate)
        )
    );
    $fullDateLabel = format(
        __('{createdDate}, edited {modifiedDate}', true),
        array(
            'createdDate' => $createdDate,
            'modifiedDate' => $modifiedDate
        )
    );
}
$canViewContent = CurrentUser::isAdmin() || CurrentUser::get('id') == $authorId;
$userProfileUrl = $html->url(array(
    'controller' => 'user',
    'action' => 'profile',
    $username
));
if ($sentenceOwnerLink) {
    $sentenceInfoLabel = __('Sentence {number} — belongs to {username}', true);
} else {
    $sentenceInfoLabel = __('Sentence {number}', true);
}

?>
<? if ($sentence) { ?>
    <div class="comment sentence" md-whiteframe="2">
        <div class="info">
            <?= format(
                $sentenceInfoLabel,
                array(
                    'number' => $sentenceLink,
                    'username' => $sentenceOwnerLink
                )
            ); ?>
        </div>
        <div layout="row" layout-align="start center">
            <div class="text" dir="<?= $langDir ?>" flex>
                <?= $sentenceText ?>
            </div>
            <?php
            echo $languages->icon(
                $sentenceLang,
                array(
                    'width' => 30,
                    'height' => 20,
                    'class' => 'lang'
                )
            );
            ?>
            <md-button class="md-icon-button" href="<?= $sentenceUrl ?>">
                <md-icon>info</md-icon>
            </md-button>
        </div>
    </div>
<? } ?>
<md-card class="comment <?= $commentHidden ? 'inappropriate' : '' ?>">
    <md-card-header>
        <md-card-avatar>
            <a href="<?= $userProfileUrl ?>">
                <img class="md-user-avatar" src="<?= $avatarUrl ?>"/>
            </a>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <a href="<?= $userProfileUrl ?>"><?= $username ?></a>
            </span>
            <span class="md-subhead ellipsis" title="<?= $fullDateLabel ?>">
                <?= $dateLabel ?>
            </span>
        </md-card-header-text>

        <? foreach ($menu as $menuItem) {
            if ($menuItem['text'] == '#') {
                $itemLabel = $replyIcon ? __('reply', true) : __('permalink', true);
            } else {
                $itemLabel = $menuItem['text'];
            }
            $confirmation = '';
            if (isset($menuItem['confirm'])) {
                $msg = $menuItem['confirm'];
                $confirmation = 'onclick="return confirm(\''.$msg.'\');"';
            }
            ?>
            <md-button class="md-icon-button" <?= $confirmation ?>
                       href="<?= $html->url($menuItem['url']) ?>"
                       aria-label="<?= $itemLabel ?>">
                <md-icon><?= $menuItem['icon'] ?></md-icon>
                <md-tooltip><?= $itemLabel ?></md-tooltip>
            </md-button>
        <? } ?>
    </md-card-header>

    <md-divider></md-divider>

    <md-card-content>
        <? if ($commentHidden) { ?>
            <div class="warning-info" layout="row" layout-align="start center">
                <md-icon>warning</md-icon>
                <p>
                    <?= format(
                        __(
                            'The content of this message goes against '.
                            '<a href="{}">our rules</a> and was therefore hidden. '.
                            'It is displayed only to admins '.
                            'and to the author of the message.',
                            true
                        ),
                        'http://en.wiki.tatoeba.org/articles/show/rules-against-bad-behavior'
                    ); ?>
                </p>
            </div>
        <? } ?>

        <? if (!$commentHidden || $canViewContent) { ?>
            <p class="content" dir="auto">
                <?= $messages->formatedContent($commentText) ?>
            </p>
        <? } ?>
    </md-card-content>
</md-card>
