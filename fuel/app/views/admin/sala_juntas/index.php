<!-- CONTENT -->
<div class="header header-dark bg-primary pb-6 content__title content__title--calendar">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6">
                    <h6 class="h2 text-white d-inline-block mb-0">Sala de juntas</h6>
                    <nav aria-label="breadcrumb" class="d-none d-lg-inline-block ml-lg-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item">
                                <?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
								Sala de juntas
							</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 mt-3 mt-lg-0 text-lg-right">
                    <a href="#" class="fullcalendar-btn-prev btn btn-sm btn-neutral">
                        <i class="fas fa-angle-left"></i>
                    </a>
                    <a href="#" class="fullcalendar-btn-next btn btn-sm btn-neutral">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <!-- FULLCALENDAR -->
            <div class="card card-calendar">
                <!-- CARD HEADER -->
                <div class="card-header">
                    <!-- TITLE -->
                    <h5 class="fullcalendar-title h3 mb-0 text-capitalize">Sala de juntas</h5>
                </div>
                <!-- CARD BODY -->
                <div class="card-body p-0">
                    <div class="calendar" data-toggle="calendar-admin" id="calendar"></div>
                </div>
            </div>
            <!-- MODAL - ADD NEW EVENT -->
            <!--* MODAL HEADER *-->
            <!--* MODAL BODY *-->
            <!--* MODAL FOOTER *-->
            <!--* MODAL INIT *-->
            <div class="modal fade" id="new-event" tabindex="-1" role="dialog" aria-labelledby="new-event-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-secondary" role="document">
                    <div class="modal-content">
                        <!-- Modal body -->
                        <div class="modal-body">
                            <form class="new-event--form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="hour-start">Hora de inicio</label>
                                            <input type="time" id="hour-start" class="form-control new-event--hour-start" value="09:00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="hour-end">Hora de final</label>
                                            <input type="time" id="hour-end" class="form-control new-event--hour-end" value="10:00">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Descripción</label>
                                    <textarea class="form-control new-event--description textarea-autosize" placeholder="Descripción del evento"></textarea>
                                    <i class="form-group--bar"></i>
                                </div>
                                <input type="hidden" class="new-event--start" />
                                <input type="hidden" class="new-event--end" />
                            </form>
                        </div>
                        <!-- MODAL FOOTER -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary new-event--add">Registrar</button>
                            <button type="button" class="btn btn-link ml-auto" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL - EDIT EVENT -->
            <!--* MODAL BODY *-->
            <!--* MODAL FOOTER *-->
            <!--* MODAL INIT *-->
            <div class="modal fade" id="edit-event" tabindex="-1" role="dialog" aria-labelledby="edit-event-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-secondary" role="document">
                    <div class="modal-content">
                        <!-- Modal body -->
                        <div class="modal-body">
                            <form class="edit-event--form">
                                <div class="form-group">
                                    <label class="form-control-label">Descripción</label>
                                    <textarea class="form-control edit-event--description textarea-autosize" placeholder="Descripción del evento"></textarea>
                                    <i class="form-group--bar"></i>
                                </div>
                                <input type="hidden" class="edit-event--id">
                            </form>
                        </div>
                        <!-- MODAL FOOTER -->
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-calendar="update">Actualizar</button>
                            <button class="btn btn-danger" data-calendar="delete">Eliminar</button>
                            <button class="btn btn-link ml-auto" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
