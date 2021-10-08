$(function () {
	
	// 抽奖封装函数
	function Win(obj, cb) {
	    this.timer = null;
	    this.startIndex = obj.startIndex - 1;
	    this.count = 0;
	    this.winningIndex = obj.winningIndex - 1;
	    this.totalCount = obj.totalCount || 6;
	    this.speed = obj.speed || 100;
	    this.domData = this.elementFormat(document.querySelector(obj.el).childNodes);
	    this.init();
	    this.cb = cb;
	}
	
	let theSingle = false;
	
	Win.prototype = {
	    init: function() {
	        if (theSingle) {
	            return
	        }
	        theSingle = true;
	        this.rollFn()
	    },
	    elementFormat: function(data) {
	        for (let i = 0; i < data.length; i++) {
	            if (data[i].nodeName === '#text' && !/\S/.test(data[i].nodeValue)) {
	                data[i].parentNode.removeChild(data[i])
	            }
	        }
	        return data
	    },
	    rollFn: function() {
	        let that = this;
	        for (let i = 0; i < this.domData.length - 1; i++) {
	            this.domData[i].className = ''
	        }
	        this.startIndex++;
	        if (this.startIndex >= this.domData.length - 1) {
	            this.startIndex = 0;
	            this.count++
	        }
	        this.domData[this.startIndex].classList.add('active');
	        if (this.count >= this.totalCount && this.startIndex === this.winningIndex) {
	            if (typeof this.cb === 'function') {
	                setTimeout(function() {
	                    that.cb();
	                    theSingle = false
	                }, 100)
	            }
	            clearInterval(this.timer)
	        } else {
	            if (this.count >= this.totalCount - 1) {
	                this.speed += 30
	            }
	            this.timer = setTimeout(function() {
	                that.rollFn()
	            }, this.speed)
	        }
	    }
	};
	
	// 中奖概率
	function getIndex(arr) {
		let leng = 0;
		for (let i=0; i<arr.length; i++) {
			leng += arr[i];
		}
		for (let i=0; i<arr.length; i++) {
			let random = parseInt( Math.random() * leng );
			if ( random < arr[i]){
				return i;
			} else {
				leng -= arr[i];
			}
		}
	}
	
	// 奖品设置（name名称，prob中奖概率 例：1/1000, id中奖下标）
	const gifts = [
		{
			name: "特等奖", 
			prob: 1,
			id:8
		},
		{
			name: "一等奖",
			prob: 5,
			id: 2
		},
		{
			name: "二等奖",
			prob: 10,
			id: 4
		},
		{
			name: "三等奖",
			prob: 50,
			id: 6
		},
		{
			name: "谢谢参与",
			prob: 934,
			id: [1,3,5,7]
		}
	];
	
	// 中奖下标值
	function prizeIndex() {
		let arrProb = [];
		for(let i=0; i<gifts.length; i++) {
			arrProb.push(gifts[i]['prob'])
		}
		let winIndex = gifts[getIndex(arrProb)]['id'];
		if(typeof(winIndex) === "object") {
			let ranIndex = Math.floor(Math.random() * 4),
				prizeIndex = winIndex[ranIndex];
			return prizeIndex
		}
		return winIndex
	}
	
	// 中奖结果方法
	function prizeRes() {
		let resLottery = "";   
		$('#lottery li').each(function () {
			if ($(this).hasClass('active')) {
				resLottery = $(this).html();
				$("body").css("position","fixed");
				$(".mask").css("display","flex");
				$(".codeBox").hide();
				switch (resLottery){
					case "谢谢参与":
						$('.priCurImg').attr("src","./images/prize_1.jpg");
						$('.title').html("温馨提示");
						$('.prizeTxt').html("不要放弃，参加活动，下次再来!");
						break;
					case "一等奖":
						$('.priCurImg').attr("src","./images/prize_1.jpg");
						$('.title').html("恭喜您");
						$('.prizeTxt').html("获得一等奖");
						break;
					case "二等奖":
						$('.priCurImg').attr("src","./images/prize_2.jpg");
						$('.title').html("恭喜您");
						$('.prizeTxt').html("获得二等奖");
						break;
					case "三等奖":
						$('.priCurImg').attr("src","./images/prize_2.jpg");
						$('.title').html("恭喜您");
						$('.prizeTxt').html("获得三等奖");
						break;
					case "特等奖":
						$('.priCurImg').attr("src","./images/prize_1.jpg");
						$('.title').html("恭喜您");
						$('.prizeTxt').html("获得特等奖");
				}
				$(".prizeBox").show();
				$(".prizeTxt").show();
				$(".btnCon").show();
				outSwitch = false;
				let $btnCon = $(".btnCon");
				let countDown = 5;
				$btnCon.addClass("btnConDis");
				$btnCon.text("确定(" + countDown + "s)");
				btnTimer = setInterval(function(){
					countDown --;
					$btnCon.text("确定(" + countDown + "s)");
					if( countDown == 0 ){
						clearInterval(btnTimer);
						btnTimer = null;
						$btnCon.removeClass("btnConDis");
						$btnCon.text("确定");
						outSwitch = true
					}
				}, 1000);
				return false;
			}
		})
	}
	
	// 抽奖实例化
	function lottery() {
		new Win({
			el: '#lottery',                  // 抽奖元素的父级
			startIndex: 0,                   // 从第几个位置开始抽奖(默认为0)
			totalCount: 4,                   // 抽奖灯圈数
			winningIndex: prizeIndex(),      // 中奖的位置索引[1-8]
			speed: 50                        // 抽奖动画的速度
		}, function () {                     // 中奖后的回调函数
			prizeRes()
		})
	}
	
	let subSwitch = true,                    // 提交按钮开关
		outSwitch = true,                    // 遮罩层开关
		meBtnSwitch = true,                  // 抽奖按钮开关
		btnTimer;                            // 定时器
	
	// 抽奖按钮样式
	$('#me').off("touchstart").on("touchstart", function () {
		$(this).css("background","#dcbb07");
		return false
	})
	
	// 点击抽奖按钮
	$('#me').off("touchend").on("touchend", function () {
		$(this).css("background","gold");
		if(meBtnSwitch){
			$('.prizeBox').hide();
			$('.lotteryEnd').hide();
			$('.btnCon').hide();
			$('.mask').css("display","flex");
			$('.title').show();
			$('.codeBox').show();
			btnTimer = setTimeout(function(){
				$('.inpVal').focus();
				clearTimeout(btnTimer);
				btnTimer = null;
			}, 500)
		}
		return false
	})
		
	// 遮罩层
	$('.mask').off('click').click(function(e){
		e.stopPropagation();
		if (e.target.className == 'mask' && outSwitch){
			$(this).hide();
			$(".inpVal").val("")
			meBtnSwitch = true;
		}
	})
	
	// 提交按钮样式
	$('.codeBtn').off("touchstart").on("touchstart", function () {
		$(this).css("background","#ca212c");
		return false
	})
	
	// 提交按钮
	$('.codeBtn').off("touchend").on("touchend", function () {
		$(this).css("background","#f13541");
		if(subSwitch) {
			subSwitch = false;
			let inputVal = $('.inpVal').val(),
				regExp = /^[a-zA-Z0-9]{8}$/,              // 6位编码
				resReg = regExp.test(inputVal),
				$tips = $('.tips');
			if( resReg ){   
				// 带着中奖编码去后端验证
				// $post("demo.php",{ prizecode:inputVal },function(data){
				// 	console.log(data)
				// })
				meBtnSwitch = false;
				$tips.text("");
				$('.mask').hide();
				lottery();
			} else {
				if ( inputVal == '' ){
					$tips.css("color","#f00").text("不能为空");
				} else {
					$tips.css("color","#f00").text("格式不正确");
				}
			}
			btnTimer = setTimeout( function(){
				subSwitch = true;
				clearTimeout(btnTimer);	
				btnTimer = null;
			}, 1000)
		}
		return false
		
	})
	
	// 中奖弹窗确认按钮
	$(".btnCon").off("click").click(function () {
		if(outSwitch) {
			$(".mask").css("display","none");
			$(".inpVal").val("");
			$("body").css("position","static");
			meBtnSwitch = true;
		}
		return false
	})
})