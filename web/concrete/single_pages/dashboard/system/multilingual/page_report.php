<?php defined('C5_EXECUTE') or die("Access Denied.");
$nav = Loader::helper('navigation');
?>

<?php if (count($sections) > 0) { ?>

<div class="ccm-dashboard-content-full">

    <form role="form" action="<?=$controller->action('view')?>" data-form="search-multilingual-pages" class="form-inline ccm-search-fields">
        <input type="hidden" name="sectionID" value="<?=$sectionID?>" />
        <div class="ccm-search-fields-row">
            <div class="form-group">
                <?=$form->label('keywords', t('Search'))?>
                <div class="ccm-search-field-content">
                    <div class="ccm-search-main-lookup-field">
                        <i class="fa fa-search"></i>
                        <?=$form->search('keywords', array('placeholder' => t('Keywords')))?>
                        <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="ccm-search-fields-row">
            <div class="form-group">
                <label class="control-label" for="sectionIDSelect"><?=t('Choose Source')?></label>
                <div class="ccm-search-field-content">
                    <?php echo $form->select('sectionIDSelect', $sections, $sectionID)?>
                </div>
            </div>
        </div>

        <div class="ccm-search-fields-row" data-list="multilingual-targets">
            <div class="form-group">
                <label class="control-label"><?=t('Choose Targets')?></label>
                <div class="ccm-search-field-content">
                <? foreach($sectionList as $sc) { ?>
                    <?php $args = array('style' => 'vertical-align: middle');
                    if ($sectionID == $sc->getCollectionID()) {
                        $args['disabled'] = 'disabled';
                    }
                    ?>
                    <div>
                        <label class="checkbox-inline">
                            <?php echo $form->checkbox('targets[' . $sc->getCollectionID() . ']', $sc->getCollectionID(), in_array($sc->getCollectionID(), $targets), $args)?>
                            <?php echo $fh->getSectionFlagIcon($sc)?>
                            <?php echo $sc->getLanguageText(). " (".$sc->getLocale().")"; ?>
                        </label>
                    </div>
                <? } ?>
                </div>
            </div>
        </div>

        <div class="ccm-search-fields-row">
            <div class="form-group">
                <label class="control-label"><?=t('Display')?></label>
                <div class="ccm-search-field-content">
                    <label class="radio-inline">
                        <?php echo $form->radio('showAllPages', 0, 0)?>
                        <?php echo t('Only Missing Pages')?>
                    </label>
                    <label class="radio-inline">
                        <?php echo $form->radio('showAllPages', 1, false)?>
                        <?php echo t('All Pages') ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="ccm-search-fields-submit">
            <button type="submit" class="btn btn-primary pull-right"><?=t('Search')?></button>
        </div>

    </form>

    <? if (count($sections) > 0) {
        $width = 100 / count($sections);
    } else {
        $width = '100';
    }?>

    <div class="table-responsive">
        <table class="ccm-search-results-table">
            <thead>
            <tr>
                <th style="width: <?=$width?>%"><span><?
                    $sourceMS = \Concrete\Core\Multilingual\Page\Section\Section::getByID($sectionID);
                    print t('%s (%s)', $sourceMS->getLanguageText(), $sourceMS->getLocale());
                    ?>
                </span></th>
                <? foreach($targetList as $sc) { ?>
                    <?php if ($section->getCollectionID() != $sc->getCollectionID()) { ?>
                        <th style="width:<?=$width?>%"><span><?
                            print $fh->getSectionFlagIcon($sc);
                            print '&nbsp;';
                            print t('%s (%s)', $sc->getLanguageText(), $sc->getLocale());
                            ?>
                        </span></th>
                    <? } ?>
                <? } ?>
            </tr>
            </thead>
            <tbody>
            <? if (count($pages) > 0) { ?>
                <? foreach($pages as $pc) { ?>
                    <tr>
                    <td>
                        <a href="<?php echo $pc->getCollectionLink()?>"><?=$pc->getCollectionName()?></a>
                        <div><small><?=$pc->getCollectionPath()?></small></div>
                    </td>
                    <? foreach($targetList as $sc) {

                        $multilingualController = Core::make('\Concrete\Controller\Backend\Page\Multilingual');
                        $multilingualController->setPageObject($pc);
                        ?>
                        <? if ($section->getCollectionID() != $sc->getCollectionID()) { ?>
                            <td><div data-multilingual-page-section="<?=$sc->getCollectionID()?>" data-multilingual-page-source="<?=$pc->getCollectionID()?>">

                                    <div data-wrapper="page">
                                    <? 						$cID = $sc->getTranslatedPageID($pc);
                                if ($cID) {
                                    $p = \Page::getByID($cID);
                                    print '<a href="' . $nav->getLinkToCollection($p) . '">' . $p->getCollectionName() . '</a>';
                                } else if ($cID === '0') {
                                    print t('Ignored');

                                } ?>
                                    </div>
                                    <div data-wrapper="buttons">
                                    <?

                                    $cParentID = $pc->getCollectionParentID();
                                    $cParent = Page::getByID($cParentID);
                                    $cParentRelatedID = $sc->getTranslatedPageID($cParent);
                                    if ($cParentRelatedID) {

                                        $assignLang = t('Re-Map');
                                        if (!$cID) {
                                            $assignLang = t('Map');
                                        }
                                        ?>
                                        <?php if (!$cID) { ?>
                                            <button class="btn btn-success btn-xs" type="button"
                                                    data-btn-action="create"
                                                    data-btn-url="<?=$multilingualController->action('create_new')?>"
                                                    data-btn-multilingual-page-source="<?php echo $pc->getCollectionID()?>"
                                                    data-btn-multilingual-section="<?php echo $sc->getCollectionID()?>"
                                            ><?=t('Create Page')?></button>
                                        <?php } ?>
                                        <button class="btn btn-info btn-xs" type="button"
                                                data-btn-action="map"
                                                data-btn-url="<?=$multilingualController->action('assign')?>"
                                                data-btn-multilingual-page-source="<?php echo $pc->getCollectionID()?>"
                                                data-btn-multilingual-section="<?php echo $sc->getCollectionID()?>"
                                            ><?php echo $assignLang?></button>
                                        <?php if ($cID !== '0' && !$cID) { ?>
                                           <button class="btn btn-warning btn-xs" type="button"
                                               data-btn-action="ignore"
                                               data-btn-url="<?=$multilingualController->action('ignore')?>"
                                               data-btn-multilingual-page-source="<?php echo $pc->getCollectionID()?>"
                                               data-btn-multilingual-section="<?php echo $sc->getCollectionID()?>"
                                           ><?=t('Ignore')?></button>
                                        <?php } ?>
                                        <?php if ($cID) { ?>
                                            <button class="btn btn-danger btn-xs" type="button"
                                                    data-btn-action="unmap"
                                                    data-btn-url="<?=$multilingualController->action('unmap')?>"
                                                    data-btn-multilingual-page-source="<?php echo $pc->getCollectionID()?>"
                                                ><?=t('Un-Map')?></button>
                                        <?php } ?>

                                    <?php } else { ?>
                                        <div class="ccm-note"><?php echo t("Create the parent page first.")?></div>
                                    <?php } ?>
                                    </div>
                                </div>
                            </td>
                        <?php } ?>
                    <?php } ?>
                <? } ?>
            <? } else {?>
                <tr>
                    <td colspan="4"><?php echo t('No pages found.')?></td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">

        replaceLinkWithPage = function(cID, sectionID, link, icon, name) {
            var $wrapper = $('div[data-multilingual-page-section=' + sectionID + '][data-multilingual-page-source=' + cID + ']');
            var newLink = '<a href="' + link + '">' + name + '<\/a>';
            $wrapper.find('div[data-wrapper=page]').html(newLink);
            $wrapper.find('div[data-wrapper=buttons]').hide();
        }

        $(function() {

            $("select[name=sectionIDSelect]").change(function() {
                $("div[data-list=multilingual-targets] input").attr('disabled', false);
                $("div[data-list=multilingual-targets] input[value=" + $(this).val() + "]").attr('disabled', true).attr('checked', false);
                $("input[name=sectionID]").val($(this).val());
                $("form[data-form=multilingual-search-pages]").submit();
            });

            $('button[data-btn-action=create]').on('click', function(e) {
                var sectionID = $(this).attr('data-btn-multilingual-section'),
                    cID = $(this).attr('data-btn-multilingual-page-source');
                e.preventDefault();

                $.concreteAjax({
                    url: $(this).attr('data-btn-url'),
                    method: 'post',
                    data: {
                        'section': sectionID,
                        'cID': cID
                    },
                    success: function(r) {
                        ConcreteAlert.notify({
                            'message': r.message,
                            'title': r.title
                        });
                        if (r.pages[0]) {
                            replaceLinkWithPage(cID, sectionID, r.link, r.icon, r.name);
                        }

                    }
                });
            });

            $('button[data-btn-action=map]').on('click', function(e) {
                var sectionID = $(this).attr('data-btn-multilingual-section'),
                    cID = $(this).attr('data-btn-multilingual-page-source'),
                    url = $(this).attr('data-btn-url');

                e.preventDefault();
                ConcretePageAjaxSearch.launchDialog(function(data) {
                    $.concreteAjax({
                        url: url,
                        method: 'post',
                        data: {
                            'destID': data.cID,
                            'cID': cID
                        },
                        success: function(r) {
                            replaceLinkWithPage(cID, sectionID, r.link, r.icon, r.name);
                        }
                    });

                });
            });

            $('button[data-btn-action=ignore]').on('click', function(e) {
                var sectionID = $(this).attr('data-btn-multilingual-section'),
                    cID = $(this).attr('data-btn-multilingual-page-source');
                e.preventDefault();
                $.concreteAjax({
                    url: $(this).attr('data-btn-url'),
                    method: 'post',
                    data: {
                        'section': sectionID,
                        'cID': cID
                    },
                    success: function(r) {
                        var $wrapper = $('div[data-multilingual-page-section=' + sectionID + '][data-multilingual-page-source=' + cID + ']');
                        $wrapper.find('div[data-wrapper=page]').html('<?=t('Ignored')?>');
                        $wrapper.find('div[data-wrapper=buttons]').hide();
                    }
                });
            });

            $('button[data-btn-action=unmap]').on('click', function(e) {
                var cID = $(this).attr('data-btn-multilingual-page-source');
                e.preventDefault();
                $.concreteAjax({
                    url: $(this).attr('data-btn-url'),
                    method: 'post',
                    data: {
                        'cID': cID
                    },
                    success: function(r) {
                        var $wrapper = $('div[data-multilingual-page-source=' + cID + ']');
                        $wrapper.find('div[data-wrapper=page]').html('<?=t('Unmapped')?>');
                        $wrapper.find('div[data-wrapper=buttons]').hide();
                    }
                });
            });

        });

    </script>

    <div class="ccm-search-results-pagination">
        <? if ($pagination->haveToPaginate()) { ?>
            <?=$pagination->renderView('dashboard');?>
        <? } ?>
    </div>
</div>


<? } else { ?>
	<p><?php echo t('You have not defined any multilingual sections for your site yet.')?></p>
<? } ?>