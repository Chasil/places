<?php
/** @var mixed $this available in shortcode render context */
?>
<div class="pm-wrapper container my-4">


    <div class="row g-4">
        <!-- Add Place Form -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong><?php echo esc_html__( 'Add Place', 'places-manager' ); ?></strong>
                </div>
                <div class="card-body">
                    <form id="pm-add-form" class="vstack gap-3">
                        <div>
                            <label class="form-label" for="pm-name"><?php echo esc_html__( 'Name', 'places-manager' ); ?> *</label>
                            <input type="text" class="form-control" id="pm-name" name="name" required>
                        </div>
                        <div>
                            <label class="form-label" for="pm-address"><?php echo esc_html__( 'Address', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-address" name="address">
                        </div>
                        <div>
                            <label class="form-label" for="pm-nip"><?php echo esc_html__( 'NIP', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-nip" name="nip">
                        </div>
                        <div>
                            <label class="form-label" for="pm-regon"><?php echo esc_html__( 'REGON', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-regon" name="regon">
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><?php echo esc_html__( 'Add', 'places-manager' ); ?></button>
                    </form>
                    <div id="pm-add-msg" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Filters + List -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <strong><?php echo esc_html__( 'Filters', 'places-manager' ); ?></strong>
                </div>
                <div class="card-body">
                    <form id="pm-filters" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="pm-f-name"><?php echo esc_html__( 'Name', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-f-name" name="name" placeholder="<?php echo esc_attr__( 'Search name…', 'places-manager' ); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="pm-f-address"><?php echo esc_html__( 'Address', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-f-address" name="address" placeholder="<?php echo esc_attr__( 'Search address…', 'places-manager' ); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="pm-f-nip"><?php echo esc_html__( 'NIP', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-f-nip" name="nip" placeholder="<?php echo esc_attr__( 'Search NIP…', 'places-manager' ); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="pm-f-regon"><?php echo esc_html__( 'REGON', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-f-regon" name="regon" placeholder="<?php echo esc_attr__( 'Search REGON…', 'places-manager' ); ?>">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <strong><?php echo esc_html__( 'Places List', 'places-manager' ); ?></strong>
                    <button id="pm-load-more" class="btn btn-outline-secondary btn-sm"><?php echo esc_html__( 'Load More', 'places-manager' ); ?></button>
                </div>
                <div class="card-body">
                    <div id="pm-list" class="vstack gap-3"></div>
                    <div id="pm-empty" class="text-muted"></div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <strong><?php echo esc_html__( 'Editable Table', 'places-manager' ); ?></strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="pm-table">
                            <thead>
                            <tr>
                                <th><?php echo esc_html__( 'Name', 'places-manager' ); ?></th>
                                <th><?php echo esc_html__( 'Address', 'places-manager' ); ?></th>
                                <th><?php echo esc_html__( 'NIP', 'places-manager' ); ?></th>
                                <th><?php echo esc_html__( 'REGON', 'places-manager' ); ?></th>
                                <th class="text-end"><?php echo esc_html__( 'Actions', 'places-manager' ); ?></th>
                            </tr>
                            </thead>
                            <tbody id="pm-table-body"><!-- filled via JS --></tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="pmEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo esc_html__( 'Edit Place', 'places-manager' ); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr__( 'Close', 'places-manager' ); ?>"></button>
                </div>
                <div class="modal-body">
                    <form id="pm-edit-form" class="vstack gap-3">
                        <input type="hidden" id="pm-edit-id">
                        <div>
                            <label class="form-label" for="pm-edit-name"><?php echo esc_html__( 'Name', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-edit-name" required>
                        </div>
                        <div>
                            <label class="form-label" for="pm-edit-address"><?php echo esc_html__( 'Address', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-edit-address">
                        </div>
                        <div>
                            <label class="form-label" for="pm-edit-nip"><?php echo esc_html__( 'NIP', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-edit-nip">
                        </div>
                        <div>
                            <label class="form-label" for="pm-edit-regon"><?php echo esc_html__( 'REGON', 'places-manager' ); ?></label>
                            <input type="text" class="form-control" id="pm-edit-regon">
                        </div>
                    </form>
                    <div id="pm-edit-msg" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo esc_html__( 'Cancel', 'places-manager' ); ?></button>
                    <button id="pm-edit-save" type="button" class="btn btn-primary"><?php echo esc_html__( 'Save', 'places-manager' ); ?></button>
                </div>
            </div>
        </div>
    </div>


</div>