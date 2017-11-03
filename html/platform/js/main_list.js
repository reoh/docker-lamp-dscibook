function jsonForColModel(csvArray){
  var temp='[';
  for(var i=0;i<csvArray[0].length;i++){
    temp+='{title:"'+csvArray[0][i]+'",width:80},';
  }
  var result=temp.substr(0,temp.length-1);
  result+=']';

return result;

}



$(function (){
var getData=function(){
	 $.ajax({

                url:'server/php/getResult.php',
                data:{
                    d:'getResult'
                },
                dataType:"json",
                success:function(data) {
                	var colArr=new Array();
                	var data2=new Array();
                    var util=new Array();
            		data2=data.shift();
                	for(var i=0;i<data2.length;i++){
            			var str='{title:"'+data2[i]+'"}';
            			var objStr=(new Function("return " + str))();
            			colArr.push(objStr);
            		}
            			var colnum=colArr.length;
            			console.log(data);
                        console.log(colArr);

                   
                        console.log(util);
                      

            			var date=data[0][1];
            	console.log(colnum+":"+tmpColnum);
            	console.log(date+":"+tmpDate);

                  for(var i=0;i<colArr.length;i++){
                            $("td:nth-child("+i+")").addClass("col-"+i);
                            if(colArr[i]["title"].indexOf('_U')!=-1){
                                console.log(i);
                                util.push(i);
                                $(".col-"+i).css("background-color","#ffffe0");
                            }else if(colArr[i]["title"].indexOf('_S')!=-1){
                                $(".col-"+i).css("background-color","#E0FFFF");
                            }
                        }
            	if(date!==tmpDate){
            	if(colnum!=tmpColnum){
            		console.log("update");
            		$('#result_table').html('<table id="resultTable" class="table table-bordered"></table>');

            		}



                	  $('#resultTable').DataTable({
    						    data: data,
    						    columns:colArr,
    						    bLengthChange:false,
    						    bPaginate:false,
    						    bJQueryUI: true,
    						    deferRender:true,
    						    scrollX:true,
    						    scroller:true,

    							columnDefs: [
    							{ targets: 1, visible: false }
                                
    							],
    							stateSave: true,
    						    destroy: true
    					});
                	 }

                	tmpColnum=colArr.length;
                	tmpDate=data[0][1];

                    var now = new Date();
                    var y = now.getFullYear();
                    var m = now.getMonth() + 1;
                    var d = now.getDate();
                    var h = now.getHours();
                    var mi = now.getMinutes();
                    var s = now.getSeconds();
                    var nowTime=y + "/" + m + "/" + d + " " + h + ":" + mi + ":" + s ;
                    //$('#getCheck').html('<ul class="list-group"></ul>');
                    //$('#getCheck').append('<li class="list-group-item">Ajax:'+nowTime+'</li>');
                     $('#getCheck').html('<h6>Calc time:'+data[0][1]+'</h6>');






                },
                error:function(data){
                    $('#getCheck').text('データを取得できません');

                }
            });
}


 var tmpColnum=0;
 var tmpDate;
 getData();




 setInterval(function(){
 		getData();
     

    },5000);




});


