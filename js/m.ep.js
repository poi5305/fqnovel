var ep = new Object();
ep.svg = function(id,appendTo,data,setting){
	/* 初始值設定 */
	this.otherRatio= 0.10;
	//this.maxListNum = 10;
	this.width = $("body").width();
	this.height = $("body").width();
	this.textFontSize = 12;
	
	/* 手動設定值 */
	if(typeof(setting)=="object"){
		for(var key in setting){
			if(typeof(setting[key])=="object"){
				for(var key2 in setting[key]){
					this[key][ley2] = setting[key][key2];
				}
			}else{
				this[key] = setting[key];
			}
		}
	}
	
	/* 新增框架 */
	var html ="";
	html+="<iframe id='"+id+"' width='"+this.width+"' height='"+this.height+"' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='svg.svg'>";
	html+="</iframe>";
	$(appendTo).append(html);
	/* 反設定 */
	var height = this.height;
	var width = this.width;
	$("#"+id).load(function(){
		$(this).contents().find("#svg").each(function(){
			this.setAttribute("height", height);
			this.setAttribute("width", width);
		});
	});
	/*資料重新格式*/
	this.reData = function(data){
		
		var nData = new Array();
		if(data.length == 0)	return nData;
		var totalValue = 0;for(var i=0;i<data.length;i++){totalValue += data[i].value};
		this.totalValue = totalValue;
		if(totalValue==0)return 0;
		data.sort(function(a,b){return b.value - a.value});//由大到小排序
		
		var nDataNum = 0;
		var oName = "其他";
		var oValue = 0;
		for(var i=0;i<data.length;i++){
			var ratio = data[i].value/totalValue;
			if(ratio>=this.otherRatio){
				nData[nDataNum] = new Object();
				nData[nDataNum].name = data[i].name;
				nData[nDataNum].value = data[i].value;
				nData[nDataNum].ratio = ratio;
				nDataNum++;
			}else{
				oValue += data[i].value;
			}
		}
		nData[nDataNum] = new Object();
		nData[nDataNum].name = oName;
		nData[nDataNum].value = oValue;
		nData[nDataNum].ratio = oValue/totalValue;
		this.data = nData;
	}
	this.reData(data);
	
	/* 圓餅圖 */
	this.circle = function(textVFunction){
		var RETURN = new Array();
		RETURN[0] = document.createElementNS("http://www.w3.org/2000/svg","g");
		var data = this.data;
		if(data==undefined)return 0;
		var padding = 2;
		var x =(this.width)/2;
		var y =(this.height)/2;
		var r = 0;
		if(x<y){
			r=x-padding;
			y=x=x+padding/2;
		}else{
			r=y-padding;
			y=x=y+padding/2;
		}
		
		//90度
		var rp = Math.PI/2;
		var tmpX =x+r*Math.cos(rp);
		var tmpY =y-r*Math.sin(rp);
		for(var i =0;i<data.length;i++){
			if(data[i].ratio <0.03)continue;
			var s = "";
			var path = "M"+x+","+y;
			path += "L"+tmpX+","+tmpY;
			
			rp -= 2*Math.PI*data[i].ratio;
			var tmpX =x+r*Math.cos(rp);
			var tmpY =y-r*Math.sin(rp);
			
			if(data[i].ratio<=0.5)var arc = "0,0,1";
			else var arc = "0,1,1";
			path += "A"+r+","+r+","+arc+","+tmpX+","+tmpY;
			path += "L"+x+","+y+"z";
			var textX = x+r/1.4*Math.cos(rp+Math.PI*data[i].ratio);
			var textY = y-r/1.4*Math.sin(rp+Math.PI*data[i].ratio);
			
			
			var g = document.createElementNS("http://www.w3.org/2000/svg","g");
			var p = document.createElementNS("http://www.w3.org/2000/svg", "path");
			p.setAttribute("d", path);
			p.setAttribute("class", "circle circle"+i);
			g.appendChild(p);
			
			var textV = Array();
			
			if(textVFunction != undefined){
				textV = textVFunction(data[i]);
			}else{
				textV[0] = data[i].name;
				textV[1] = (data[i].ratio*100).toFixed()+"%";
				textV[2] = data[i].value;
			}
			for(var t=0;t<textV.length;t++){
				var tt = document.createElementNS("http://www.w3.org/2000/svg", "text");
				tt.setAttribute("class", "text text"+i);
				tt.setAttribute("x",textX);
				tt.setAttribute("y",(textY-(this.textFontSize/2+1)*(textV.length-1)+(this.textFontSize+1)*t));
				tt.setAttribute("font-size",this.textFontSize);
				tt.textContent= textV[t];
				g.appendChild(tt);
			}
			
			RETURN[i+1] = g;
		}
		$("#"+id).load(function(){
			$(this).contents().find("#svg").each(function(){
				for(var i = 0;i<RETURN.length;i++){
					this.appendChild(RETURN[i]);
				}
			});
		});
	}
}





/* export page */
ep.showType = 0; // 0->circle, 1->rect
ep.dateType = 1; //0->year, 1->month, 2->day
ep.date = new Date();
ep.date.setHours(0,0,0,0);
ep.itemId = 0;
ep.account = 0; // 0->all, other->other
ep.pn = 2; //0-> -, 1-> +, 2->all

ep.data = new Array();


ep.init = function(){
	ep.initPage();
}
ep.initPage = function(){
	ep.getData();
	ep.drawInit();
}
ep.drawInit =function(){
	setTimeout("ep.draw()",500);
}
ep.draw = function(){
	ep.svgChart = new ep.svg("chart","#svgChart",ep.data);
	ep.svgChart.circle();
}


ep.getData = function(){
	ep.data = new Array();
	
	/* readInfo */
	if(ep.itemId == 0){
		$("#svgChartInfo").html("分類：目錄");
	}else{
		db.query("SELECT * FROM item WHERE id ="+ep.itemId,function(r){
			if(r.rows.length==1){
				$("#svgChartInfo").html("分類："+r.rows.item(0).name);
			}
		});

	}
	
	/* 父分類資料讀取 */
	ep.data[0] = new Object({"id":ep.itemId,"name":"父分類","value":0});
	db.query(ep.getTMoneySQL(ep.itemId),function(r){
		if(r.rows.length==1){
			if(r.rows.item(0).tMoney != null)ep.data[0].value=r.rows.item(0).tMoney;
		}
	});
		
	/* 第一層Child多name */
	var sql ="SELECT id,name";
	sql+=",("+ep.getTMoneySQL("item.id")+") tMoney";
	sql+=" FROM item";
	sql+=" WHERE pid="+ep.itemId;
	db.query(sql,function(r){
		for(var i=0;i<r.rows.length;i++){
			var idx = ep.data.length;
			ep.data[idx] = new Object();
			ep.data[idx].id=r.rows.item(i).id;
			ep.data[idx].name=r.rows.item(i).name;
			//alert(r.rows.item(i).tMoney)
			if(r.rows.item(i).tMoney == null)	ep.data[idx].value=0;
			else	ep.data[idx].value=r.rows.item(i).tMoney;
			
			ep.getChildValue(r.rows.item(i).id,idx);
		}
	});
}
/* 第二層以後資料讀取 */
ep.getChildValue=function(pid,idx){
	var sql ="SELECT id";
	sql+=",("+ep.getTMoneySQL("item.id")+") tMoney";
	sql+=" FROM item";
	sql+=" WHERE pid="+pid;
	db.query(sql,function(r){
		for(var i=0;i<r.rows.length;i++){
			if(r.rows.item(i).tMoney != null)
			ep.data[idx].value+=r.rows.item(i).tMoney;
		}
	});
}
ep.getDataIdxByKV=function(data,key,value){
	for(var i in data){
		if(data[i][key] == undefined)continue;
		if(data[i][key] == value)return i;
	}
	return null;
}
ep.getTMoneySQL=function(itemId){
	var tmpDate = new Date(ep.date.getTime());
	var sql = "";
	sql+="SELECT SUM(money)";
	sql+=" FROM record"
	sql+=" WHERE itemId="+itemId+" AND ownerId="+mb.id;
	if(ep.account != 0)sql+=" AND accountId="+ep.account;
	if(ep.pn!=2)sql+=" AND pn="+ep.pn;
	if(ep.dateType==0){//year
		tmpDate.setMonth(0,1);// year/1/1
		var minDate = cal.reTime(tmpDate.getTime());
		tmpDate.setFullYear(tmpDate.getFullYear()+1);//year+1 /1/1
		var maxDate = cal.reTime(tmpDate.getTime());
	}else if(ep.dateType==1){//month
		tmpDate.setDate(1);
		var minDate = cal.reTime(tmpDate.getTime());
		tmpDate.setMonth(tmpDate.getMonth()+1);
		var maxDate = cal.reTime(tmpDate.getTime());
	}else{
		var minDate = cal.reTime(tmpDate.getTime());
		tmpDate.setDate(tmpDate.getDate()+1);
		var maxDate = cal.reTime(tmpDate.getTime());
	}
	sql+=" AND date >="+minDate+" AND date < "+maxDate;
	return sql;
}













