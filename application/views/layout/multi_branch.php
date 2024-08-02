<!-- Modal -->
<div id="multiBranchSwitchModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <form action="<?php echo site_url('admin/multibranch/branch/switch') ?>" method="POST" id="form_branch_change">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('switch_branch'); ?></h4>
                </div>
                <div class="modal-body branchSwitchbody">

                </div>
               
            </div>

        </form>
    </div>
</div>

<script type="text/javascript">
     $('#multiBranchSwitchModal').on('show.bs.modal', function (event) {
      
        var $modalDiv = $(event.delegateTarget);
        $('.branchSwitchbody').html("");
        $.ajax({
            type: "POST",
            url: baseurl + "admin/multibranch/branch/switchbranchlist",
            dataType: 'JSON',
            data: {},
            beforeSend: function () {
                $modalDiv.addClass('modal_loading');
            },
            success: function (data) {
                $('.branchSwitchbody').html(data.page);
            },
            error: function (xhr) { // if error occured
                $modalDiv.removeClass('modal_loading');
            },
            complete: function () {
                $modalDiv.removeClass('modal_loading');
            },
        });
    });

    $(document).on('change', '.branch_chg', function () {
        if ($(this).is(":checked")) {

            $('input.branch_chg').not(this).prop('checked', false);
        } 

    });

    $("#form_branch_change").on('submit', (function (e) {
        e.preventDefault();

         var form = $(this);
        var $this = $(this).find("button[type=submit]:focus");
        $.ajax({
            url: form.attr('action'),
            type: "POST",
            data: form.serialize(), // serializes the form's elements.
            dataType: 'json',
          
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (res)
            {
                    if (res.status) {
                        successMsg(res.message);
                        $('#multiBranchSwitchModal').modal('hide');
                        window.location.href = baseurl + "admin/multibranch/branch";

                    } else {
                        errorMsg(res.message);
                    }
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }

        });
    }));
</script>