
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense App - Dashboard</title>
    <link rel="stylesheet" href="<?php echo constant('URL') ?>public/css/expense.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <div id="main-container">
            
        <div id="history-container">
            <div id="history-options">
                <div id="filter-container">
                    Filtrar por fecha
                    <select id="sdate">
                        <option value=""></option>
                        <?php
                            $options = $this->dates;
                            foreach($options as $option){
                                echo "<option value=$option >".$option."</option>";
                            }
                        ?>
                </select>
                </div>

                <div id="filter-container">
                    Filtrar por categoría
                    <select id="scategory">
                        <option value=""></option>
                        <?php
                            $options = $this->categories;
                            foreach($options as $option){
                                echo "<option value=$option >".$option."</option>";
                            }
                        ?>
                </select>
                </div>
                
            </div>
            <table width="100%" cellpadding="0">
                <thead>
                    <tr>
                    <th data-sort="title" width="35%">Nombre</th>
                    <th data-sort="category">Tipo</th>
                    <th data-sort="date">Fecha</th>
                    <th data-sort="amount">Cantidad</th>
                    <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="databody">
                    <?php

                        foreach ($this->history as $expense) {
                       
                    ?>
                    <tr>
                        <td><?php echo $expense['expense_title']; ?></td>
                        <td><?php echo $expense['category_name']; ?></td>
                        <td><?php echo $expense['date']; ?></td>
                        <td>$<?php echo number_format($expense['amount'], 2); ?></td>
                        <td>Eliminar</td>
                    </tr>
                    <?php
                      }
                    ?>
                </tbody>
                <tfoot>
                      <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td id="total"></td>
                      </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <?php require 'views/footer.php'; ?>

    <script>
        var data = [];
        var copydata = [];
        const sdate     = document.querySelector('#sdate');
        const scategory = document.querySelector('#scategory');
        const sorts = document.querySelectorAll('th');

        sdate.addEventListener('change', e =>{
            const value = e.target.value;
            if(value === '' || value === null){
                this.copydata = [...this.data];
                checkForFilters(scategory);
                //renderData(this.copydata);
                return;
            }
            filterByDate(value);
        });

        scategory.addEventListener('change', e =>{
            const value = e.target.value;
            if(value === '' || value === null){
                this.copydata = [...this.data];
                checkForFilters(sdate);
                //renderData(this.copydata);
                return;
            }
            filterByCategory(value);
        });

        function checkForFilters(object){
            if(object.value != ''){
                //console.log('hay un filtro de ' + object.id);
                switch(object.id){
                    case 'sdate':
                        filterByDate(object.value);
                    break;  

                    case 'scategory':
                        filterByCategory(object.value);
                    break;
                    default:
                }
            }else{
                this.datacopy = [...this.data]; 
                renderData(this.datacopy);
            }
        }

        sorts.forEach(item =>{
            item.addEventListener('click', e =>{
                if(item.dataset.sort){  
                        sortBy(item.dataset.sort);        
                }
            });
        });

        function sortBy(name){
            this.copydata = [...this.data];
            let res;
            switch(name){
                case 'title':
                    res = this.copydata.sort(compareTitle);
                break;
                    
                case 'category':
                    res = this.copydata.sort(compareCategory);
                    break;

                case 'date':
                    res = this.copydata.sort(compareDate);
                    break;
                        
                case 'amount':
                    res = this.copydata.sort(compareAmount);
                    break;

                    default:
                    res = this.copydata;
            }

            renderData(res);
        }

        function compareTitle(a, b){
            if(a.expense_title.toLowerCase() > b.expense_title.toLowerCase()) return 1;
            if(b.expense_title.toLowerCase() > a.expense_title.toLowerCase()) return -1;
            return 0;
        }
        function compareCategory(a, b){
            if(a.category_name.toLowerCase() > b.category_name.toLowerCase()) return 1;
            if(b.category_name.toLowerCase() > a.category_name.toLowerCase()) return -1;
            return 0;
        }
        function compareAmount(a, b){
            if(a.amount > b.amount) return 1;
            if(b.amount > a.amount) return -1;
            return 0;
        }
        function compareDate(a, b){
            if(a.date > b.date) return 1;
            if(b.date > a.date) return -1;
            return 0;
        }

        function filterByDate(value){
            //this.copydata = [...this.data];
            const res = this.copydata.filter(item =>{
                return value == item.date.substr(0, 7);
            });
            this.copydata = [...res];
            renderData(res);
        }
        function filterByCategory(value){
            //this.copydata = [...this.data];
            const res = this.copydata.filter(item =>{
                return value == item.category_name;
            });
            this.copydata = [...res];
            renderData(res);
        }

        async function getData(){
            data = await fetch('http://localhost/expense-app/expenses/getHistoryJSON')
            .then(res =>res.json())
            .then(json => json);
            this.copydata = [...this.data];
            console.table(data);
            renderData(data);
        }
        getData();

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function renderData(data){
            var databody = document.querySelector('#databody');
            let total = 0;
            databody.innerHTML = '';
            data.forEach(item => { 
                total += item.amount;
                databody.innerHTML += `<tr>
                        <td>${item.expense_title}</td>
                        <td>${item.category_name}</td>
                        <td>${item.date}</td>
                        <td>$${numberWithCommas(parseFloat(item.amount))}</td>
                    </tr>`;
            });

            document.querySelector('#total').textContent = "$" + numberWithCommas(total);
        }
        

        
    </script>
</body>
</html>