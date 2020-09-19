        <table id="results" class="list">
          <thead>
    	      <tr class="thead">
    	        <td class="td1">Дата</td>
    	        <td class="td2">Тест</td>
				      {$is_manager != '' ? '<td class="td7">Студент</td>' : ''}
    	        <td class="td3">Результат</td>
    	        <td class="td4">Цель</td>
    	        <td class="td5 select-filter">Статус</td>
    	        <td class="td6"></td>
    	      </tr>
          </thead>
     	    <tbody class="tbody">
            {$output ?: '<tr><td style="text-align: center; padding: 10px 0;" colspan="6">Нет данных для отображения</td></tr>'}
          </tbody>
   	    </table>