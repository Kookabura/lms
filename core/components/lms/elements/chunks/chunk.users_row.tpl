    <tr id="row[[+id]]">
        <td class="td0">
    	    <input id="CheckBox[[+id]]" name="id[]" value="[[+id]]" type="checkbox" class="CheckBoxClass">
            <label for="CheckBox[[+id]]" class="CheckBoxLabelClass"></label>
        </td>
        <td class="td1">[[+user.fullname:default=`[[+username]]`]]</td>
        <td class="td2">[[+user.email]]</td>
        <td class="td3">[[+role.name]]</td>
        <td id="status[[+id]]" class="td10 status">
            [[+active:eq=`1`:then=`<i class="fa fa-check green"></i>`:else=`<i class="fa fa-times red"></i>`]]
            <span style="display: none;">[[+active]]</span>
        </td>
    </tr>