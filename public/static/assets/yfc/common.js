function getTimeSelectOptions(defTime= '',interval = 15){
    var hourArray = [];
    for (var h = 0; h < 24; h++) {
        var t = h < 10 ? '0' + h : h
        var t_1 = t + ':00', t_2 = t + ':15', t_3 = t + ':30', t_4 = t + ':45';
        hourArray.push(t_1, t_2, t_3, t_4);
    }
    var html = '';
    for (var s = 0; s < hourArray.length; s++) {
        var check = '';
        if (hourArray[s] == defTime) {
            check = 'selected';
        }
        html += '<option value="' + hourArray[s] + '" ' + check + '>' + hourArray[s] + '</option>';
    }
    return html
}