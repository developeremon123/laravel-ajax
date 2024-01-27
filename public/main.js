function select_all(){
    if($('#select_all:checked').length == 1){
        $('.select_data').prop('checked', true);
    }else{
        $('.select_data').prop('checked', false);
    }
}

function select_single_item(id){
    let total = $('.select_data').length;//count total checkbox
    let total_checked = $('.select_data:checked').length; //count total checked checkbox
    (total == total_checked) ? $('#select_all').prop('checked',true) : $('#select_all').prop('checked',false);
}