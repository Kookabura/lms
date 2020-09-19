      <div class="left">
         <div class="title">Добавить пользователя</div>
         <p class="btitle">Email пользователя будет его логином. Пароль придет пользователю по почте.</p>
         <form id="studentForm" action="[[~[[*id]]]]" method="post">
          <div class="fleft">
            <input type="text" placeholder="Имя" name="fullname"/>
            <select name="role">
              [[+professions]]
            </select>
          </div>
          <div class="fright">
            <input type="email" placeholder="Email" name="username"/>
            <div class="check_box">
              <input id="CheckBox" type="checkbox" class="CheckBoxClass" name="active" value="1">
              <label id="Label" for="CheckBox" class="CheckBoxLabelClass">Вкл</label>
            </div>
            <input type="submit" value="Добавить" />
          </div>
         </form>
      </div>