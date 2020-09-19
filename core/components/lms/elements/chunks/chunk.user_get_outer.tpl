<form action="[[~[[*id]]]]" method="post" id="updateStudentForm">
    <input type="hidden" name="action" value="">
    <div class="list_users">
        <div class="tables">
            <table id="users" class="list">
              <thead>
                <tr>
                    <td class="td0">&nbsp;</td>
                    <td class="td1">Имя</td>
                    <td class="td2">Email</td>
                    <td class="td3">Специальность</td>
                    <td class="td10">Состояние</td>
                </tr>
              </thead>
              <tbody class="tbody">
                    [[+output]]
              </tbody>
            </table>
        </div>
      <div class="buttons">
        <!-- Single button -->
        <div class="btn-group">
          <button type="submit" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Действие <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li><a data-action="student/activatemultiple" href="#">Включить</a></li>
            <li><a data-action="student/deactivatemultiple" href="#">Отключить</a></li>
            <li><a data-action="student/removemultiple" href="#">Удалить</a></li>
          </ul>
        </div>
      </div>
    </div>
</form>