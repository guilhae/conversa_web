<?php require('head.php');?>
    <style>
    #conversa{
        max-height: 100%;
        overflow-x: hidden;
    }

    /* Scrollbar */
    #conversa::-webkit-scrollbar {
        width: 6px;
    }
    #conversa::-webkit-scrollbar-track {
        background: transparent;
    }
    #conversa::-webkit-scrollbar-thumb {
        background-color: rgba(255,255,255,0.2);
        border-radius: 8px;
        border: none;
    }

    .rounded-end{
        border-top-right-radius: 32px!important;
        border-bottom-right-radius: 32px!important;
    }
    
    .conversa_a, .conversa_b{
        max-width: 75%;
    }
    </style>
    <script>
        function Som(){
            var audio = document.getElementById("notification_sound");
            audio.play();
        }
    </script>
</head>
<body>

<audio id="notification_sound"><source src='sounds/sons.wav'></audio>

<div id="erro_offline" class="position-absolute w-100 h-100 bg-opacity-75 bg-dark d-none" style="z-index: 10;">
    <section class="position-absolute top-50 start-50 translate-middle alert bg-opacity-75 bg-dark border-vermelho p-0">
        <div class="alert text-center bg-opacity-10 bg-vermelho m-0 px-5">
            <text><i class="h4 bi bi-exclamation-triangle"></i><br>Estás disconectado</text>
        </div>
    </section>
</div>

<div class="col-xl-6 offset-xl-3 my-0 p-0 min-vh-100" style="background-color:#150f29;">

<?php
if ($_GET["id"]){
    echo '
    
    <div id="loading" class="position-absolute top-50 start-50 translate-middle">
        <div class="spinner-border text-secondary" role="status"></div>
    </div>

    <div id="conversa_header" class="d-flex flex-row p-3 align-items-center bg-opacity-25 bg-dark">
        <a href="/app" class="text-light"><i class="h4 bi bi-arrow-left"></i></a>
        <img id="conversa_icon" class="mt-1 me-2 rounded-circle" height="64">
        <span class="col"><h3 id="conversa_title" class="mt-1 mb-0"></h3>
        <small> </small></span>
    </div>
    
    <section class="pe-2">
        <section id="conversa" class="pe-3 py-5"></section>
    </section>
    
    <section class="row">
        <form id="form_mensagem" class="position-absolute bottom-0 start-50 translate-middle-x">
            <div class="d-flex p-3 col-xl-6 offset-xl-3">
                <div class="flex-grow-1 me-2">
                    <input id="input_mensagem" autocomplete="off" autofocus class="form-control border-0 bg-dark" placeholder="Mensagem">
                </div>
                <div>
                    <button class="btn btn-conversa"><i class="bi bi-send"></i></button>
                </div>
            </div>
        <form>
    </section>

    ';
} else {
    echo "
    <div class='p-4'>
        <h1>as tuas conversas</h1>

        <section id='carregando' class='text-center p-4'>
            <div class='spinner-border' role='status'>
                <span class='sr-only'></span>
            </div>
        </section>

        <button id='btn_nova_conversa' onclick='open_nova_conversa()' class='btn btn-conversa'>Nova conversa <i class='bi bi-chat-left-text'></i></button>
        <button id='btn_fechar' onclick='close_nova_conversa()' class='btn btn-conversa' style='display:none;'>Fechar <i class='bi bi-x-lg'></i></button>
        
        <section id='nova_conversa' class='row row-cols-1 row-cols-md-2'>
        </section>

        <div id='lista_conversas'>
        </div>

    </div>
    ";
    
    $caixa_utilizador = '
        <div class="col pt-0 pb-2 p-1">
            <div class="alert bg-primary bg-opacity-25 d-flex align-items-center p-2 m-0" role="alert">
                <a class="perfil" href="https://drena.pt/u/\'+d.nut+\'">
                <img class="rounded-circle me-2" src="\'+d.fpe+\'" width="38">\'+d.nut+\'</a>
                <button onclick="criar_chat(`\'+d.nut+\'`)" class="btn bg-light bg-opacity-10 text-light ms-auto m-0">Nova <i class="bi bi-chat-left-text"></i></button>
            </div>
        </div>
    ';

    $caixa_conversa = '
    <a href="/app?id=\'+d.id+\'" class="text-light text-decoration-none">
        <div class="d-flex flex-row my-2">
            <img src="\'+img_conversa+\'" class="mt-1 me-2 rounded-circle" height="64">
            <span class="col">
                <text class="h5">\'+nome_conversa+\'</text><br>
                \'+mensagem+\'<br>
                <sup>\'+tempoPassado(d.lastMessages[0].date)+\'</sup>
                
            </span>
        </div>
    </a>
    ';
}
?>

</div>


<?php

$mensagem_b = '
<div class="conversa_b d-inline-flex my-3 px-3 py-1 bg-opacity-10 rounded-end bg-light">
    <img src="`+utis[f_user].fpe+`" class="mt-2 me-2 rounded-circle" height="32">
    <span class="col pe-4 pb-1" id="`+f_id+`">
        <small><span class="text-light">`+f_hour+` — </span>`+f_user+`</small><br>
        <text>`+f_msg+`</text>
    </span>
</div><br>
';

$mensagem_a = "
<div id='`+f_id+`' class='conversa_a text-end ms-auto'>
    <small class='text-light'>`+f_hour+`</small>
    <br><text>`+f_msg+`</text>
</div>
";

?>

<?php
if ($_GET["id"]){
    echo "<!--";
}
?>
<script>
//LISTA CONHECIDOS

var amigos;

function criar_chat(f_uti){
    startChat = api('https://conversa.drena.pt:3000/startChat', JSON.stringify({'receivers': [f_uti],'text': 'Olá!'}), true, 'application/json');
    window.location.href = "?id="+startChat.id;
}

function open_nova_conversa(){
    if (!amigos){
        amigos = api('https://drena.pt/api/ob_ami', {"uti": uti.nut});
        //onsole.debug(amigos);
        $.each(amigos, function (k, d) {
            $('#nova_conversa').append('<?php echo preg_replace( "/\r|\n/", "", $caixa_utilizador); ?>');
        })
        console.debug("Amigos carregados");
    }
    $('#btn_nova_conversa').hide();
    $('#lista_conversas').hide();
    $('#btn_fechar').show();
    $('#nova_conversa').show();
}

function close_nova_conversa(){
    $('#btn_nova_conversa').show();
    $('#lista_conversas').show();
    $('#btn_fechar').hide();
    $('#nova_conversa').hide();
}


//Obter conversas
conversas = api('https://conversa.drena.pt:3000/getChat', null, true, null);

$("#carregando").addClass("d-none");
if (conversas=="error"){
    $("#erro_offline").removeClass("d-none");
} else {
    console.debug("Conversas:");
    console.debug(conversas);
    //Ordena a array por data da última mensagem em cada conversa
    conversas.sort((a, b) => {
        const aDate = new Date(a.lastMessages[0].date);
        const bDate = new Date(b.lastMessages[0].date);
        return bDate - aDate;
    });

    //Carregar utilizadores
    var lista_utis = [];
    var utis = [];
    $.each(conversas, function (k, d) {
        if (d.type=="DIRECT_MESSAGE"){
            lista_utis.push(d.users[0].username);
        }
    });
    console.debug("Lista de utilizadores:");
    console.debug(lista_utis);
    $.each( api('https://drena.pt/api/uti', {'utis': JSON.stringify(lista_utis)}) , function (k, d) {
        utis[d.nut] = {'nco':d.nco,'fpe':d.fpe,'dcr':d.dcr};
    });
    console.debug("Utilizadores:");
    console.debug(utis);


    $.each(conversas, function (k, d) {
        if (d.type=="DIRECT_MESSAGE"){
            nome_conversa = d.users[0].username;
            img_conversa = utis[d.users[0].username].fpe;
            mensagem = d.lastMessages[0].content;
            if (d.lastMessages[0].username==uti){
                mensagem = "<small>Eu: </small>"+mensagem;
            }
        } else {
            nome_grupo = "Eu";
            $.each(d.users, function (key, user) {
                nome_grupo += ", "+user.username;
            });
            nome_conversa = nome_grupo;
            img_conversa = "/img/grupo.jpg";
            mensagem = d.lastMessages[0].content;
            if (d.lastMessages[0].username==uti){
                mensagem = "<small>Eu: </small>"+mensagem;
            } else {
                mensagem = "<small>"+d.lastMessages[0].username+": </small>"+mensagem;
            }
        }
        $('#lista_conversas').append('<?php echo preg_replace( "/\r|\n/", "", $caixa_conversa); ?>');
    });
}
</script>
<?php
if ($_GET["id"]){
    echo "-->";
}
?>





<?php
if (!$_GET["id"]){
    echo "<!--";
}
?>
<script>
    // FUNÇÕES (Início)
    var ultimo_uti;
    var ultimo_id;
    var lista_utis = [];
    var utis = [];

    function carregar_utis(conversa){
        $.each(conversa.users, function (k, d) {
            lista_utis.push(d.username);
        });
        $.each( api('https://drena.pt/api/uti', {'utis': JSON.stringify(lista_utis)}) , function (k, d) {
            utis[d.nut] = {'nco':d.nco,'fpe':d.fpe,'dcr':d.dcr};
        });
        console.debug("Utilizadores:");
        console.debug(utis);
    }

    function renderizarMensagem(f_id,f_msg,f_user,f_date){
        //console.log({f_id, f_msg, f_user, f_date});

        if (f_date){
            f_hour = new Date(f_date).toString("HH:mm");
        }

        if (ultimo_uti==f_user){
            $('#'+ultimo_id).append(`<br><text>`+f_msg+`</text>`);
        } else {
            if (f_user==uti.nut){
                $('#conversa').append(`<?php echo preg_replace( "/\r|\n/", "", $mensagem_a); ?>`);
            } else {
                $('#conversa').append(`<?php echo preg_replace( "/\r|\n/", "", $mensagem_b); ?>`);
            }
            ultimo_id = f_id;
        }
        ultimo_uti = f_user;
    }

    function irParaBaixo(){
        $("#conversa").stop().animate({ scrollTop: $("#conversa")[0].scrollHeight}, 0);
    }
    // FUNÇÕES (Fim)

    // Construir página
    var header_height = $('#conversa_header').outerHeight();
    var footer_height = $('#form_mensagem').outerHeight();
    var remove_height = header_height+footer_height+20;
    $('#conversa').css({'height':'calc(100vh - '+remove_height+'px)'});

    chatID = '<?php echo $_GET['id'];?>';
    const socket = io('https://conversa.drena.pt:3000');

    //Conexão ao socket
    socket.on('connect', () => {
        $("#erro_offline").addClass("d-none");
        $("#conversa").html("");

        socket.emit('enterChat', {'id': chatID, 'token': token});
        console.debug('Connected to server');
    });

    socket.on("chatInfo", (data) => {
        let { chatInfo } = data;
        let { lastMessages } = chatInfo;

        console.debug("Info da conversa:");
        console.debug(chatInfo);        
        console.debug("Ultimas mensagens:");
        console.debug(lastMessages);

        //Carrega informações dos utilizadores da conversa (fpe essencialmente)
        carregar_utis(chatInfo);

        //Renderiza as mensagens
        $.each(lastMessages.reverse(), function (k, d) {
            renderizarMensagem(d.id,d.content,d.username,d.date);
        });
        irParaBaixo();

        //Escolhe o icon e o nome da conversa
        if (chatInfo.type=="DIRECT_MESSAGE"){
            conversa_title = chatInfo.users[0].username;
            conversa_icon = utis[chatInfo.users[0].username].fpe;
        } else {
            conversa_title = "Eu";
            $.each(chatInfo.users, function (k, d) {
                conversa_title += ", "+d.username;
            });
            conversa_icon = "/img/grupo.jpg";
        }
        $("#conversa_title").html(conversa_title);
        $("#conversa_icon").attr("src", conversa_icon);

        //Esconde o loading
        $("#loading").addClass("d-none");
    });

    socket.on("errorOccurred", (error) => {
        if (error.type === "not-authenticated") {
        } else if (error.type === "not-authorized") {
        } else if (error.type === "empty-message") {
        } else if (error.type === "chat-doesnt-exist") {
        } else {
        }
        alert("ERRO: "+error.type+"\n"+error.message);
    });

    socket.on('disconnect', () => {
        $("#erro_offline").removeClass("d-none");
        console.debug('Disconnected to server');
    });

    socket.on('message', (data) => {
        renderizarMensagem(data.id,data.content,data.username,data.date);
        irParaBaixo()
        Som()
    });

    $('#form_mensagem').on('submit', function(e) {
		e.preventDefault();
		var mensagem = $('#input_mensagem').val();

        socket.emit('message', mensagem);

        renderizarMensagem(String(Date.now()),mensagem,uti.nut);

		$('#input_mensagem').val('');

        irParaBaixo()
	});
</script>
<?php
if (!$_GET["id"]){
    echo "-->";
}
?>

</body>
</html>