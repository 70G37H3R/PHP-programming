<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container">
    <section id="inner" class="inner-section section">
        <div class="container">

            <!-- SECTION HEADING -->
            <h2 class="section-heading text-center wow fadeIn" data-wow-duration="1s">Contacts</h2>
            <div class="row">
                <div class="col-md-6 col-md-offset-3 text-center">
                    <p class="wow fadeIn" data-wow-duration="2s">Add your contacts here.</p>
                </div>
            </div>

            <div class="inner-wrapper row">
                <div class="col-md-12">

                    <form name="frm" id="frm" action="/contacts" method="post" class="col-md-6 col-md-offset-3">

                        <input type="hidden" name="_csrf_token" value="<?= \App\Csrf::getToken() ?>">

                        <!-- Name -->
                        <div class="form-group<?= isset($errors['name']) ? ' has-error' : '' ?>">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" maxlen="255" id="name" placeholder="Enter Name" value="<?= isset($old['name']) ? $this->e($old['name']) : '' ?>" />

                            <?php if (isset($errors['name'])) : ?>
                                <span class="help-block">
                                    <strong><?= $this->e($errors['name']) ?></strong>
                                </span>
                            <?php endif ?>
                        </div>

                        <!-- Phone -->
                        <div class="form-group<?= isset($errors['phone']) ? ' has-error' : '' ?>">
                            <label for="phone">Phone Number</label>
                            <input type="text" name="phone" class="form-control" maxlen="255" id="phone" placeholder="Enter Phone" value="<?= isset($old['phone']) ? $this->e($old['phone']) : '' ?>" />

                            <?php if (isset($errors['phone'])) : ?>
                                <span class="help-block">
                                    <strong><?= $this->e($errors['phone']) ?></strong>
                                </span>
                            <?php endif ?>
                        </div>

                        <!-- Description -->
                        <div class="form-group<?= isset($errors['notes']) ? ' has-error' : '' ?>">
                            <label for="description">Notes </label>
                            <textarea name="notes" id="notes" class="form-control" placeholder="Enter notes (maximum character limit: 255)"><?= isset($old['notes']) ? $this->e($old['notes']) : '' ?></textarea>

                            <?php if (isset($errors['notes'])) : ?>
                                <span class="help-block">
                                    <strong><?= $this->e($errors['notes']) ?></strong>
                                </span>
                            <?php endif ?>
                        </div>

                        <!-- Submit -->
                        <button type="submit" name="submit" id="submit" class="btn btn-primary">Add Contact</button>
                    </form>

                </div>
            </div>

        </div>
    </section>
</div>
<?php $this->stop() ?>

<?php $this->start("page_specific_js") ?>
<script>
    $(document).ready(function() {
        new WOW().init();
        setTimeout(function() {
            <?php
            if ($_GET['status'] == 'success') {
                echo 'alert("welldone");';
            } else {
                echo 'alert("no good");';
            }
            ?>
        }, 500);
    });
</script>
<?php $this->stop() ?>