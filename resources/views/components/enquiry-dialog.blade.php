@props(['serviceOptions' => []])

<dialog class="enquiry-dialog" aria-labelledby="enquiry-dialog-title" data-enquiry-dialog>
    <div class="enquiry-dialog-panel">
        <div class="enquiry-dialog-heading">
            <div>
                <p class="eyebrow eyebrow-navy"><span class="eyebrow-rule"></span> START A CONVERSATION</p>
                <h2 id="enquiry-dialog-title">Tell us about your project.</h2>
                <p>Share a few details and we will follow up.</p>
            </div>
            <button class="enquiry-dialog-close" type="button" aria-label="Close enquiry form" data-enquiry-close>
                <span aria-hidden="true">×</span>
            </button>
        </div>

        <form action="{{ route('enquiries.store') }}" method="post" data-enquiry-form>
            @csrf
            <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden"><label for="enquiry-website">Leave this field empty</label><input id="enquiry-website" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="enquiry-fields">
                <div class="enquiry-field">
                    <label for="enquiry-name">Your name <span aria-hidden="true">*</span></label>
                    <input id="enquiry-name" name="name" type="text" autocomplete="name" maxlength="120" required aria-describedby="enquiry-name-error">
                    <span class="enquiry-field-error" id="enquiry-name-error" data-enquiry-error="name"></span>
                </div>
                <div class="enquiry-field">
                    <label for="enquiry-email">Email address <span aria-hidden="true">*</span></label>
                    <input id="enquiry-email" name="email" type="email" autocomplete="email" maxlength="254" required aria-describedby="enquiry-email-error">
                    <span class="enquiry-field-error" id="enquiry-email-error" data-enquiry-error="email"></span>
                </div>
                <div class="enquiry-field">
                    <label for="enquiry-company">Organisation <span class="enquiry-optional">Optional</span></label>
                    <input id="enquiry-company" name="company" type="text" autocomplete="organization" maxlength="180" aria-describedby="enquiry-company-error">
                    <span class="enquiry-field-error" id="enquiry-company-error" data-enquiry-error="company"></span>
                </div>
                <div class="enquiry-field">
                    <label for="enquiry-service">Service area <span class="enquiry-optional">Optional</span></label>
                    <select id="enquiry-service" name="service" aria-describedby="enquiry-service-error" data-enquiry-service-select>
                        <option value="">Choose a service area</option>
                        @foreach ($serviceOptions as $serviceOption)
                            <option value="{{ is_array($serviceOption) ? $serviceOption['title'] : $serviceOption->title }}">{{ is_array($serviceOption) ? $serviceOption['title'] : $serviceOption->title }}</option>
                        @endforeach
                    </select>
                    <span class="enquiry-field-error" id="enquiry-service-error" data-enquiry-error="service"></span>
                </div>
                <div class="enquiry-field enquiry-field-full">
                    <label for="enquiry-message">How can we help? <span aria-hidden="true">*</span></label>
                    <textarea id="enquiry-message" name="message" rows="5" maxlength="5000" required aria-describedby="enquiry-message-error"></textarea>
                    <span class="enquiry-field-error" id="enquiry-message-error" data-enquiry-error="message"></span>
                </div>
            </div>

            <div class="enquiry-dialog-footer">
                <p class="enquiry-feedback" role="status" aria-live="polite" data-enquiry-feedback></p>
                <button class="button button-gold enquiry-submit" type="submit" data-enquiry-submit>Send enquiry <span aria-hidden="true">↗</span></button>
            </div>
        </form>
    </div>
</dialog>
