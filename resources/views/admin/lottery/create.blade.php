<!-- 内容区 -->
<div id="main" class="mainWid">
	<link rel="stylesheet" href="/css/choujiang.css">
	<div class="listBox">
		<div class="active">
			<div class="ruleBox">
				<form class="ruleForm">
					<div>
						<label>长度:</label>
						<div>8位</div>
					</div>
					<div>
						<label>规则:</label>
						<div>数字加字母</div>
					</div>
					<div>
						<label>生成数量:</label>
						<input type="text" class="codeNum" name="number" placeholder="设置抽奖码生成的数量" autocomplete="off"  maxlength="7">
						<p class="numTips"></p>
					</div>
					<div>
						<label>有效期至:</label>
						<input type="text" class="expDay" name="date" placeholder="例: 20210925" autocomplete="off" maxlength="8">
						<p class="expTips"></p>
					</div>
					<div>
						<button class="codeBtn" type="button">生成抽奖码</button>
					</div>
				</form>
			</div>
			<div class="lotyTab">
				<div class="tabHead">
					<div>序号</div>
					<div>批次号</div>
				</div>
				<ul class="tabMain">
					@foreach($batch_nums as $key=>$batch)
					<li>
						<div>{{$key+1}}</div>
						<div>{{$batch->batch_num}}</div>
					</li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
  // 不同意 按钮的点击事件
  $('.codeBtn').click(function(e) {
  	e.preventDefault();
  	var nums = $('.codeNum').val();
  	var date = $('.expDay').val();
  	$.ajax({
        url: '{{route('admin.generateCode')}}',
        type: 'POST',
      	data: JSON.stringify({// 将请求变成 JSON 字符串
        	agree: false,  // 拒绝申请
        	nums: nums,
        	date: date,
        	// 带上 CSRF Token
        	// Laravel-Admin 页面里可以通过 LA.token 获得 CSRF Token
        	_token: LA.token,
   		}),
      contentType: 'application/json',  // 请求的数据格式为 JSON
      success: function(data){
      		if(data.code = 200){
	         	swal({
				        title: '生成抽奖码成功',
				        type: 'success'
				    }).then(function() {
				        // 用户点击 swal 上的按钮时刷新页面
				        location.reload();
				    });
			  	}else{
			  		wal({
				        title: '生成抽奖码失败',
				        type: 'error'
				    });
			  	}
      }
    });
  });
});
</script>
