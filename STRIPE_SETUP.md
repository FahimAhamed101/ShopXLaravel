# Stripe setup

ShopX uses Stripe-hosted Checkout with signed webhook fulfillment. The installed stable PHP SDK is `stripe/stripe-php` v21.2.0.

## Sandbox

1. Open the Stripe Dashboard in sandbox mode and copy the publishable and secret keys.
2. Add a webhook destination pointing to:

   ```text
   https://your-domain.example/stripe/webhook
   ```

3. Subscribe it to these events (recommended for reliable fulfillment):

   - `checkout.session.completed`
   - `checkout.session.async_payment_succeeded`

4. Copy the destination's signing secret and configure `.env`. Stripe Checkout can be enabled with the API keys alone, but production stores should configure the webhook so orders are fulfilled even when the customer does not return to the success page:

   ```dotenv
   STRIPE_STATUS=active
   STRIPE_MODE=sandbox
   STRIPE_CURRENCY=USD
   STRIPE_RATE=1
   STRIPE_KEY=pk_test_replace_me
   STRIPE_SECRET=sk_test_replace_me
   STRIPE_WEBHOOK_SECRET=whsec_replace_me
   ```

5. Clear cached configuration:

   ```shell
   php artisan optimize:clear
   ```

For local webhook testing with Stripe CLI:

```shell
stripe listen --forward-to http://127.0.0.1:8000/stripe/webhook
```

Use the `whsec_...` value printed by the CLI as `STRIPE_WEBHOOK_SECRET` while the listener is running.

## Live mode

Create a separate live webhook destination with the same event subscriptions. Change `STRIPE_MODE` to `live` and replace all three credentials with their live values (`pk_live_...`, `sk_live_...` or an appropriately permissioned `rk_live_...`, and the live endpoint's `whsec_...`). Never commit secret or webhook keys.
