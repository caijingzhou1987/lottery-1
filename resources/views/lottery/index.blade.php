<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>幸运大抽奖</title>
		<meta name="full-screen" content="yes">
		<meta name="x5-fullscreen" content="true">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
		<link rel="stylesheet" href="css/lottery.css" />
	</head>
	<body>
		<!-- 头部背景区 -->
		<div class="top">
			<img src="storage/images/bg_1.png" alt="背景图_1">
		</div>
		
		<!-- 主体抽奖区 -->
		<div class="body">
			<img src="storage/images/bg_2.jpg" alt="背景图_2">
			<div class="main">
				<ul id="lottery">
					@foreach($prizes as $prize)
					<li>	
						<img src="storage/{{$prize->url}}" data-prize="{{$prize->title}}" alt="奖品图片">
					</li>
					@endforeach
					<li id="me">抽奖</li>
				</ul>
			</div>
		</div>
		
		<!-- 尾部说明区 -->
		<div class="footer">
			<h1>活动说明：</h1>
			<p>1.活动说明活动说明活动说明活动说明</p>
			<p>2.活动说明活动说明活动说明活动说明活</p>
			<p>3.活动说明活动说明活动说明活动说明活动</p>
			<p>4.活动说明活动说明活动说明活动说明活动说明</p>
		</div>
		
		<!-- 弹窗区 -->
		<div class="mask">
			<div class="popup">
				<!-- 提示标题 -->
				<h2 class="title">请输入抽奖码</h2>
				
				<!-- 中奖编码输入 -->
				<div class="codeBox">
					<input class="inpVal" type="text">
					<p class="tips"></p>
					<button class="codeBtn" type="button">提交</button>
				</div>
				
				<!-- 奖品区 -->
				<div class="prizeBox">
					<div class="prizeImg">
						<p>
							<img class="priCurImg" src="" alt="奖品图片">
						</p>
					</div>
					<h3 class="prizeTxt"></h3>
				</div>
				
				<!-- 抽奖后提示 -->
				<div class="lotteryEnd">您已经抽过奖了!</div>
				<div class="btnCon">确定</div>
			</div>
		</div>
		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript">
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
				// 中奖结果方法
				function prizeRes(code) {
					let resLottery = "";   
					$('#lottery li').each(function () {
						if ($(this).hasClass('active')) {
							resLottery = $(this).html();
							$("body").css("position","fixed");
							$(".mask").css("display","flex");
							$(".codeBox").hide();
							$('.priCurImg').attr("src",$(this).find('img').attr('src'));
							$('.title').html("温馨提示");
							$('.prizeTxt').html($(this).find('img').data('prize')+'('+code+')');
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
				function lottery(prizeIndex,code) {
					new Win({
						el: '#lottery',                  // 抽奖元素的父级
						startIndex: 0,                   // 从第几个位置开始抽奖(默认为0)
						totalCount: 4,                   // 抽奖灯圈数
						winningIndex:prizeIndex,      // 中奖的位置索引[1-8]
						speed: 50                        // 抽奖动画的速度
					}, function () {                     // 中奖后的回调函数
						prizeRes(code)
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
							$.get('{{ route('lotteryCode') }}',{code:inputVal}, function(data){
							    if(data.code == 0){
							    	$tips.css("color","#f00").text(data.message);
								}
							    if(data.code == 200){
							    	$('.mask').hide();
							    	lottery(data.index,data.number);	
							    }
							});
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
		</script>
	</body>
</html>
