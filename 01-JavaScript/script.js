window.onload=init();

function init(){
    var medData = ["Paracetamol_row01_col01", "Paracetamol_row01_col02", "Paracetamol_row01_col03", "Atorvastatin_row02_col01","Atorvastatin_row02_col02", "Atorvastatin_row02_col03", "Melatonin_row03_col01", "Melatonin_row03_col02", "Melatonin_row03_col03"];
	var sortMedBtn = document.getElementById("sortMeds");
	sortMedBtn.addEventListener("click", sortMedRecords);
}

function sortMedRecords(){
	medData.sort(sortAndShow);
	changeRowColours();
}

function sortAndShow(a,b){
    var nameA = a.medData.toLowerCase(), nameB = b.medData.toLowerCase();
	
	if(nameA < nameB)
	{
		return -1
	}
	
	if(nameA > nameB)
	{
		return 1
	}
	else{
		return 0
	}
}

function changeRowColours(){
    var tableElements = document.getElementsByTagName("table");
	
	for(var j = 0; j < tableElements.length; j++){
        var table = tableElements[j];
		var rows = table.getElementsByTagName("tr");
		
        for(var i = 0; i <= rows.length; i++){
            if(i%2==0){
                rows[i].style.backgroundColor = "red";
            }else{
                rows[i].style.backgroundColor = "blue";
            }
        }
    }
}
