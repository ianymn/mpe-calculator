<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPE Calculator</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="img-container">
        <img src="ST_logo_2020_blue_V_rgb.png" width="200" height="150" id="logo"> 
        <div class="name-container">
            MPE CALCULATOR
        </div>
    </div>

    <div id="file-list">
        <br>
        <?php 
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            include("readFile.php"); 
        ?>
    </div>

    <label id="instruction-label">Click a product to test.</label>

    <div id="main-container">
        <div id="left-container">
            <div id="total-qty-container" style="display: none;">
                <label for="totalQty">Total Quantity:</label>
                <input type="number" id="totalQty" name="totalQty">
            </div>

            <div id="input-data-container" style="display: none;">
                <div id="data-container">
                    <label id="prod-label">Loading. . .</label><br><br>
                    <label id="file-update-label">Loading...</label><br><br>
                </div>
                <div id="input-container">   
                </div>
            </div>
            <button id="compare-btn" style="display: none;">Compare</button>
        </div>

        <div id="action-container">
        </div>
    </div>

    <script>
        function checkQuarterlyAlert(startDate, endDate, currentDate, currentMonth, currentDay) {
                        
            const quarterMonths = [1, 4, 7, 10];
            endDate.setDate(startDate.getDate() + 13);

            if (quarterMonths.includes(currentMonth)) {
                const storedAlertDate = localStorage.getItem('alertDate');
                const alertDate = storedAlertDate ? new Date(storedAlertDate) : null;

                console.log("Stored Alert Date: ", storedAlertDate);
                console.log("Alert Date: ", alertDate);

                // Reset alertDate at the start of each new quarter
                if (!alertDate || alertDate < startDate) {
                    localStorage.removeItem('alertDate');
                }

                if (currentDate >= startDate && currentDate <= endDate) {                        
                        alert("This is your quarterly alert! Please check for latest MPE limit(s)");
                        localStorage.setItem('alertDate', currentDate.toISOString());                    
                }
            }
        }

        function checkFileUpdate(filePath, startDate, endDate, currentDate, currentMonth, currentDay, response) {
            const lastModified = response.headers.get('Last-Modified');
            console.log("Response received, Last-Modified:", lastModified); 
            const fileUpdateLabel = document.getElementById('file-update-label');
            const prodLabel = document.getElementById('prod-label'); 

            endDate.setDate(startDate.getDate() + 6);//change to 13 for testing only
            
            console.log("Current Date: ", currentDate);
            console.log("Start Date: ", startDate);
            console.log("End Date: ", endDate);                        

                if (lastModified) {
                    const lastModifiedDate = new Date(lastModified);
                    const temp = filePath.substring(filePath.lastIndexOf('/') + 1);  
                    console.log("Last Modiefied Date: ", lastModifiedDate);
                    if (currentDate >= startDate && currentDate <= endDate) {
                        alert("Limits for "+ temp.substring(0, temp.length - 4) + " has recently been changed");
                    }    
                    prodLabel.textContent = temp.substring(0, temp.length - 4);                 
                    fileUpdateLabel.textContent = `Last Updated: ${lastModifiedDate.toLocaleString()}`;
                    console.log("Updated file-update-label:", fileUpdateLabel.textContent); 
                } else {
                    fileUpdateLabel.textContent = 'File Last Updated: Unknown';
                    console.log("Updated file-update-label to unknown"); 
                }
                return response.text();
        }

        function fetchFileData(filePath) {
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1;
            const currentDay = currentDate.getDate();
            const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const endDate = new Date(startDate);

            checkQuarterlyAlert(startDate, endDate, currentDate, currentMonth, currentDay);

            const timestamp = new Date().getTime();
            const url = `${filePath}?t=${timestamp}`;
            
            console.log("Fetching file data from:", filePath); 
            fetch(url)
                .then(response => checkFileUpdate(filePath, startDate, endDate, currentDate, currentMonth, currentDay, response))
                .then(textData => {
                    console.log("Text data received:", textData);
                    const lines = textData.trim().split('\n');
                    const data = lines.map(line => {
                        const [limits, errors] = line.split(',');
                        return {
                            limits: parseFloat(limits),
                            errors: errors.trim()
                        };
                    });
                    console.log("Parsed data: ", data);

                    const dataContainer = document.getElementById('data-container');
                    const inputContainer = document.getElementById('input-container');

                    dataContainer.querySelectorAll('.data-item').forEach(item => item.remove());
                    inputContainer.innerHTML = '';

                    data.forEach((item, index) => {
                        const inputGroup = document.createElement('div');
                        inputGroup.className = 'input-group';

                        const div = document.createElement('div');
                        div.className = 'data-item';
                        div.textContent = `Limit: ${item.limits}, Error: ${item.errors}`;
                        dataContainer.appendChild(div);

                        const label = document.createElement('label');
                        label.htmlFor = `error-input-${index}`;
                        label.textContent = `${item.errors}:`;

                        const input = document.createElement('input');
                        input.type = 'number';
                        input.id = `error-input-${index}`;
                        input.name = `error-input-${index}`;

                        const warningLabel = document.createElement('span');
                        warningLabel.id = `warning-label-${index}`;
                        warningLabel.textContent = ' Exceeds Limit';
                        warningLabel.style.color = 'red';
                        warningLabel.style.display = 'none';

                        input.addEventListener('input', function() {
                            const inputValue = parseFloat(input.value);
                            const limit = parseFloat(input.dataset.limit);
                            const warningLabel = document.getElementById(`warning-label-${index}`);

                            const totalQtyValue = document.getElementById('totalQty').value;
                            const totalQty = parseFloat(totalQtyValue);

                            const ratio = inputValue / totalQty;

                            if (ratio > limit) {
                                warningLabel.style.display = 'inline';
                            } else {
                                warningLabel.style.display = 'none';
                            }
                        });

                        input.dataset.limit = item.limits;

                        inputGroup.appendChild(label);
                        inputGroup.appendChild(input);
                        inputGroup.appendChild(warningLabel);

                        inputContainer.appendChild(inputGroup);
                    });

                    document.getElementById('total-qty-container').style.display = 'block';
                    document.getElementById('input-data-container').style.display = 'flex';
                    document.getElementById('compare-btn').style.display = 'block';
                    document.getElementById('instruction-label').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching the data: ', error);
                });
        }

        document.querySelectorAll('.file-link').forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const filePath = this.getAttribute('data-filepath');
                fetchFileData(filePath);
            });
        });

        document.getElementById('compare-btn').addEventListener('click', function() {
            const totalQtyValue = document.getElementById('totalQty').value;
            const totalQty = parseFloat(totalQtyValue);
            const inputs = document.querySelectorAll('#input-container input[type="number"]');
            let exceeded = false;
            let emptyTextbox = false;

            if (isNaN(totalQty) || totalQty <= 0) {
                alert('Please enter a valid Total Quantity that is not zero.');
                return;
            }
            let totalDmg = 0;
            const inputElements = document.querySelectorAll('#input-container input[type="number"]');
            inputs.forEach((input, index) => {
                const inputValue = parseFloat(input.value);
                const limit = parseFloat(input.dataset.limit);
                const warningLabel = document.getElementById(`warning-label-${index}`);

                if (isNaN(inputValue)) {
                    emptyTextbox = true;
                }
                
                const ratio = (inputValue / totalQty) * 100; //percentage of impact on yield on each error
                const min = Math.floor(totalQty * (limit/100)); //minimum value allowable for each limit based on totalQty

                if (ratio > limit) {
                    warningLabel.style.display = 'inline';
                    warningLabel.textContent = ` (${ratio.toFixed(2)}%. Min of: ${min.toFixed(0)} allowed. )`;
                    exceeded = true;
                } else {
                    warningLabel.style.display = 'none';
                }

                totalDmg += inputValue;
            });

            const percentage = ((totalQty - totalDmg)/ totalQty) * 100; //total yield percentage
            const actionContainer = document.getElementById('action-container');
            actionContainer.innerHTML = '';

            if (emptyTextbox) {
                const label = document.createElement('label');
                label.textContent = '❌ UNCOMPLETE ❌, Kindly complete the form';
                actionContainer.appendChild(label);
            } else if (percentage < 98 || exceeded) {                
                const label = document.createElement('label');
                label.textContent = '❌ HOLD ❌ MPE Threshold Limit. Please secure all reject units for Process Tech Validation Total Yield of ' + percentage.toFixed(2) +'%';
                actionContainer.appendChild(label);
            } else {
                const label = document.createElement('label');
                label.textContent = '✔️ MPE PASSED ✔️ Total Yield of '+ percentage.toFixed(2) +'%';
                actionContainer.appendChild(label);
            }

            if (exceeded) {
                document.getElementById('instruction-label').textContent = '❌ HOLD ❌ MPE Threshold Limit. Please secure all reject units for Process Tech Validation';
            }

            actionContainer.style.display = 'block'; 
        });

        // Trigger the alert check on page load
        window.onload = function() {
            checkQuarterlyAlert();
        };
    </script>
</body>
</html>