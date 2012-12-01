<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach( $breadcrumbs as $breadcrumb ){ ?>
        <?php echo $breadcrumb[ 'separator' ]; ?><a
                href="<?php echo $breadcrumb[ 'href' ]; ?>"><?php echo $breadcrumb[ 'text' ]; ?></a>
        <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if (!empty($this->session->data['success'])) { ?>
    <div class="success"><?php echo $this->session->data['success']; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/order.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_run; ?></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><?php echo $entry_name; ?></td>
                        <td><input type="file" name="file" id="file" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_type; ?></td>
                        <td>
                            <select name="type" id="type">
                                <option value="1">Тип 1 (Б)</option>
                                <option value="2">Тип 2 (Ф)</option>
                                <option value="3">Тип 3 (П)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_category; ?></td>
                        <td><select name="category_id">
                            <option value="0" selected="selected"><?php echo $text_none; ?></option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>