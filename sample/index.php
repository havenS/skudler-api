<?php include_once(dirname(dirname(__FILE__)).'/src/SkudlerAPI.php');

$apikey = '';
$token  = '';

if(isset($_POST['apiKey']) && isset($_POST['token'])) {

    $apikey = $_POST['apiKey'];
    $token  = $_POST['token'];

    $skudler = new Skudler\SkudlerAPI($apikey, $token);

    $sites = $skudler->getSites(true);

    if (isset($_POST['site'])) {
        $events = $skudler->getEvents($_POST['site'], true);
    }
    if (isset($_POST['subscriber']) && isset($_POST['event'])) {
        $subscription = $skudler->addSubscription($_POST['event'], $_POST['subscriber']);
    }

    if ($skudler->error)
        echo ' /!\ ' . $skudler->error . ' /!\ ';
}
?>

<form method="post">

    <label for="apiKey">API Key</label>
    <input type="text" id="apiKey" name="apiKey" value="<?php echo $apikey;?>">

    <label for="token">Token</label>
    <input type="text" id="token" name="token" value="<?php echo $token;?>">


    <?php if(!empty($sites)){ ?>

        <label for="sites">Sites</label>
        <select id="sites" name="site">
            <?php foreach($sites as $s){ ?>
                <option value="<?php echo $s->_id;?>"
                    <?php if(isset($_POST['site']) && $_POST['site'] == $s->_id) echo ' selected';?>><?php echo $s->name;?></option>
            <?php } ?>
        </select>

    <?php } ?>

    <?php if(!empty($events)){ ?>
        <hr>

        <label for="events">Events</label>
        <select id="events" name="event">
            <?php foreach($events as $t){ ?>
                <option value="<?php echo $t->_id;?>"><?php echo $t->name;?></option>
            <?php } ?>
        </select>

        <label for="firstname">Firstname</label>
        <input type="text" name="subscriber[firstname]" id="firstname">

        <label for="lastname">Lastname</label>
        <input type="text" name="subscriber[lastname]" id="lastname">

        <label for="email">Email</label>
        <input type="email" name="subscriber[email]" id="email">

        <label for="reference_date">Reference date</label>
        <input type="text" name="subscriber[reference_date]" id="reference_date" value="<?php echo date('Y-m-d H:i:s', strtotime('now'));?>">

    <?php } ?>

    <hr>

    <button>Send</button>
</form>