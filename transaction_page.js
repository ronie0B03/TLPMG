$(document).ready(() => 
{
    $("#table_items_barcode").on('click', '._btn_add_item', function()
        {
            const tbody    = $("#table_items_barcode > tbody");
            const id       = jQuery.trim($("#table_items_barcode ._input_id").val());
            const barcode  = jQuery.trim($("#table_items_barcode ._input_barcode").val());
            const quantity = jQuery.trim($("#table_items_barcode ._input_quantity").val());
            const price    = jQuery.trim($("#table_items_barcode ._input_price").val());
            const subtotal = jQuery.trim($("#table_items_barcode ._input_subtotal").val());

            const row      =    `<tr>
                                    <td>
                                        <input type="hidden" name="item_id[]" value="${id}">
                                        <div class='d-flex justify-content-center align-items-center border-none'>
                                            <button type="button" class="btn btn-danger btn-sm _btn_remove_item">Remove Item</button>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="items[]" value="${barcode}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="item_quantity[]" value="${quantity}" placeholder = "0" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="item_price[]" value="${price}" step = "0.0001" placeholder = "0.00" readonly>
                                    </td>
                                    <td><input type="text"   class="form-control _item_subtotal"  value="${subtotal}" readonly></td>
                                </tr>`

            tbody.append(row);      

            $("#table_items_barcode ._input_id").val(''); 
            $("#table_items_barcode ._input_barcode").val(''); 
            $("#table_items_barcode ._input_price").val(0); 
            $("#table_items_barcode ._input_quantity").val(0); 
            $("#table_items_barcode ._input_subtotal").val(0); 

            $(this).prop('disabled', true); 
            $("#table_items_barcode ._input_quantity").prop('disabled', true); 

            let totalAmount     =  0; 

            tbody.find('._item_subtotal').each(function(index) 
                {
                    totalAmount += +$(this).val();
                }
            );

            $('#total_amount_barcode').html(totalAmount);
        }
    );

    $("#table_items_barcode").on('click', '._btn_remove_item', function()
        {
            const subtotal = $(this).parent().parent().parent().find('._item_subtotal').val(); 
            const total    = $('#total_amount_barcode').html();
            const amount   = (total - subtotal);

            $(this).parent().parent().parent().remove();

            console.table(subtotal);
            $('#total_amount_barcode').html(amount);
        }
    );

    $("#table_items_barcode").on('input', '._input_quantity', function()
        {
            const quantity = $(this).val();
            const price    = $("#table_items_barcode ._input_price").val();
            const subTotal = $("#table_items_barcode ._input_subtotal");

            if(!jQuery.trim(quantity) == "" || quantity > 0)
            {
                $("#table_items_barcode ._btn_add_item").prop('disabled', false);
                subTotal.val(+price * +quantity)
            }   
            else 
            {
                $("#table_items_barcode ._btn_add_item").prop('disabled', true); 
                subTotal.val(0);
            }                     
        }
    );

    $("#table_items_barcode").on('input', '._input_barcode', function()
        {
            const barcode = $(this).val()

            $.get(
                "ajax/fetch_item.php", 
                {
                    'getBarcode' : true, 
                    'barcode'    : barcode, 

                },
                function(response)
                {
                    const res = JSON.parse(response);

                    if(res.success)
                    {
                        $("#table_items_barcode ._input_quantity").prop('disabled', false);                                                         
                        $("#table_items_barcode ._input_id").val(res.item.id); 
                        $("#table_items_barcode ._input_price").val(res.item.item_price); 
                    }
                    else 
                    {
                        $("#table_items_barcode ._btn_add_item").prop('disabled', true); 
                        $("#table_items_barcode ._input_quantity").prop('disabled', true);
                        
                        $("#table_items_barcode ._input_id").val(""); 
                        $("#table_items_barcode ._input_quantity").val(0); 
                    }
                }
            )
        }
    );
});