<form method="post" action="">
    <h2><?php _e('Choose a Subscription Plan', 'saas-subscription-manager'); ?></h2>
    <select name="subscription_plan">
        <option value="basic"><?php _e('Basic - $10/month', 'saas-subscription-manager'); ?></option>
        <option value="premium"><?php _e('Premium - $20/month', 'saas-subscription-manager'); ?></option>
    </select>
    <button type="submit" name="subscribe"><?php _e('Subscribe', 'saas-subscription-manager'); ?></button>
</form>
<?php
if (isset($_POST['subscribe'])) {
    $plan = $_POST['subscription_plan'];
    $amount = ($plan === 'basic') ? 10 : 20;

    $checkout_url = MSM_Payment_Handler::create_checkout_session($amount, 'usd', 'Subscription');
    wp_redirect($checkout_url);
    exit;
}
?>
