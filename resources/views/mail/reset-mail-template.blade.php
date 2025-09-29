<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> إعادة تعيين كلمة المرور </title>
    <style>
        .head-line {
            height: 15px;
            background-color:#e7000b;
        }

        .footer-line {
            height: 15px;
            background-color: lightgray;
            margin-top: 20px;
        }

        .site-name {
            text-align: center;
            font-size: 30px;
            color:#e7000b;
            padding: 10px;
        }

        .code-style {
            padding-left: 5px;
            padding-right: 5px;
			   padding-top: 5px;
			   padding-bottom: 5px;
        }
 .info {
            padding-left: 5px;
            padding-right: 5px;
			   padding-top: 5px;
			   padding-bottom: 5px;
        }
        .code-div {
            text-align: right;
            font-size: 16px;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="row"></div>
    <div class="col-12">
        <div class="head-line"></div>
        <p class="lead site-name">{{ $data['com_title'] }}</p>
        <div class="lead code-div">           
            <p><span><strong >{{ $data['marketer_mail'] }}</strong></span> <span>: تم إعادة تعيين كلمةالمرور للحساب </span></p>
             
			   <div class="info"> <span>   :كلمة المرور الجديدة    </span></div>
               <div class="info" style="text-align: center"> <span>{{ $data['new_pass'] }} </span></div>
        </div>
        <div class="footer-line"></div>
    </div>
</body>

</html>
