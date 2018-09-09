<?php
/** @var Template $this */
/** @var ViewHelper $view */

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;
use Zend\Diactoros\UploadedFile;

$this->layout('layouts/layout');
$form = $view->forms();
$data = $view->data();
/** @var UploadedFile $upload */
$upload = $view->data()->get('upload', null);

?>

<?php $this->start('contents'); ?>

<h1>File Upload</h1>

<?= $view->message ?>

<h2>Upload Form</h2>

<p>please upload any file less than 512 byte. </p>

<?= /** form open for upload */
$form->open()->method('post')->uploader(); ?>
    <input type="hidden" name="_token" value="<?= $view->csrfToken();?>">
<?= $form->hidden('MAX_FILE_SIZE', 512); ?>

<?= /** file upload element. */
$form->formGroup(
    $form->label('file to upload', 'up'),
    $form->file('up[0]')->class('form-control')->width('70%')->id('up')
); ?>

<?= $form->submit('upload file')->class('btn btn-primary'); ?>

<?= /** end of form */
$form->close(); ?>

<?php if ($upload) : ?>

    <h2>Uploaded File Information</h2>

    <dl class="dl-horizontal">

        <dt>getClientFilename</dt>
        <dd><?= $upload->getClientFilename() ?></dd>

        <dt>getClientMediaType</dt>
        <dd><?= $upload->getClientMediaType() ?></dd>

        <dt>getSize</dt>
        <dd><?= $upload->getSize() ?></dd>

        <dt>getError</dt>
        <dd><?= $upload->getError() ?></dd>

    </dl>

<?php endif; ?>
<?php if ($data->dump) : ?>

    <h2>dump of getUploadedFile() method</h2>
    <pre>
    <?= $data->dump; ?>
    </pre>

<?php endif; ?>

<?php $this->stop(); ?>