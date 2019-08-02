<?php
/**
 * Checkout terms and conditions checkbox
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) : ?>
<?php do_action( 'woocommerce_checkout_before_terms_and_conditions' ); ?>
<p class="form-row terms wc-terms-and-conditions">
	<input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
	<label for="terms" class="checkbox"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" id="termAndConditionsId">terms &amp; conditions</a>', 'woocommerce' ), 'javascript:void(0);' ); ?> <span class="required">*</span></label>
	<input type="hidden" name="terms-field" value="1" />
</p>
<?php do_action( 'woocommerce_checkout_after_terms_and_conditions' ); ?>
<?php endif; ?>
<div id="termAndConditions" class="modal fade termAndConditions configurationsummary" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Terms and Conditions</h4>
			</div>
			<div class="modal-body">
				<div class="terms_conditions">
					Terms and Conditions for the Online Sale of Goods and Services<p></p>
					<ol>
						<li><strong>This document contains very important information regarding your rights and obligations, as well as conditions, limitations, and exclusions that might apply to you. Please read it carefully. </strong></li>
					</ol>
					<p><strong>These terms require the use of arbitration to resolve disputes, rather than jury trials. </strong></p>
					<p><strong>By placing an order for products or services from this website, you affirm that you are of legal age to enter into this agreement, and you accept and are bound by these terms and conditions. You affirm that if you place an order on behalf of an organization or company, you have the legal authority to bind any such organization or company to these terms.</strong></p>
					<p><strong>You may not order or obtain products or services from this website if you (I) do not agree to these terms, (ii) are not the older of (a) at least 18 years of age or (b) legal age to form a binding contract with megladon manufacturing group, ltd. Or (iii) are prohibited from accessing or using this website or any of this website’s contents, products or services by applicable law.</strong></p>
					<p>These terms and conditions (these “<strong>Terms</strong>“) apply to the purchase and sale of products and services through www.megladonmfg.com (the “<strong>Site</strong>“). These Terms are subject to change by Megladon Manufacturing Group, Ltd. (referred to as “<strong>us,</strong>” “<strong>we,</strong>” or “<strong>our</strong>” as the context may require) without prior written notice at any time, in our sole discretion. Any changes to these Terms will be in effect as of the “Last Updated Date” referenced on the Site. You should review these Terms prior to purchasing any product or services that are available through this Site. Your continued use of this Site after the “Last Updated Date” will constitute your acceptance of and agreement to such changes.</p>
					<p>These Terms are an integral part of the Website Terms of Use that apply generally to the use of our Site. You should also carefully review our Privacy Policy before placing an order for products or services through this Site (see Section 10).</p>
					<ol start="2">
						<li><u><span style="color: #003366;">Order Acceptance and Cancellation</span></u>. You agree that your order is an offer to buy, under these Terms, all products and services listed in your order. All orders must be accepted by us or we will not be obligated to sell the products or services to you. We may choose not to accept orders at our sole discretion, even after we send you a confirmation email with your order number and details of the items you have ordered.</li>
						<li><u><span style="color: #003366;">Prices and Payment Terms</span></u>.
							<ul>
								<li>All prices, discounts, and promotions posted on this Site are subject to change without notice. The price charged for a product or service will be the price advertised on this Site at the time the order is placed, subject to the terms of any promotions or discounts that may be applicable. The price charged will be clearly stated in your order confirmation email. Price increases will only apply to orders placed after the time of the increase. Posted prices do not include taxes or charges for shipping and handling. All such taxes and charges will be added to your total price, and will be itemized in your shopping cart and in your order confirmation email. We strive to display accurate price information, however we may, on occasion, make inadvertent typographical errors, inaccuracies or omissions related to pricing and availability. We reserve the right to correct any errors, inaccuracies, or omissions at any time and to cancel any orders arising from such occurrences.</li>
								<li>Terms of payment are within our sole discretion and, unless otherwise agreed by us in writing, payment must be received by us before our acceptance of an order. We accept all major credit cards for all purchases. You represent and warrant that (i) the credit card information you supply to us is true, correct and complete, (ii) you are duly authorized to use such credit card for the purchase, (iii) charges incurred by you will be honored by your credit card company, and (iv) you will pay charges incurred by you at the posted prices, including shipping and handling charges and all applicable taxes, if any, regardless of the amount quoted on the Site at the time of your order.</li>
							</ul>
						</li>
						<li><u><span style="color: #003366;">Shipments; Delivery; Title and Risk of Loss</span></u>.
							<ul>
								<li>We will arrange for shipment of the products to you. Please check the individual product page for specific delivery options. You will pay all shipping and handling charges unless otherwise specified in the order confirmation.</li>
								<li>Title and risk of loss pass to you upon our transfer of the products at delivery. Shipping and delivery dates are estimates only and cannot be guaranteed. We are not liable for any delays in shipments.</li>
							</ul>
						</li>
						<li><u><span style="color: #003366;">Returns and Refunds</span></u>. All purchases through our website are non-cancelable, non-returnable.</li>
						<li><u><span style="color: #003366;">Limited Warranty</span></u>.
							<ul>
								<li>We warrant to you that for a period of twelve (12) months from the date of shipment (“<strong>Warranty Period</strong>“), the products purchased through the Site will materially conform to our published specifications in effect as of the date of manufacture and be free from material defects in material and workmanship.</li>
								<li>We warrant to you that we shall perform the services purchased through the Site using personnel of required skill, experience and qualifications and in a professional and workmanlike manner in accordance with generally recognized industry standards for similar services and shall devote adequate resources to meet our obligations under these Terms.</li>
								<li><strong>except for the warranties set forth in </strong><a href="#a967734"><strong>section 6(a)</strong></a><strong> and </strong><a href="#a701571"><strong>section 6(b)</strong></a><strong>, we make no warranty whatsoever with respect to the products or services purchased through the site, including any (I) warranty of merchantability; (ii) warranty of fitness for a particular purpose; (iii) warranty of title; or (iv) warranty against infringement of intellectual property rights of a third party; whether express or implied by law, course of dealing, course of performance, usage of trade, or otherwise.</strong></li>
								<li>Products manufactured by a third party (“<strong>Third Party Product</strong>“) may constitute, contain, be contained in, incorporated into, attached to or packaged together with, the products. Third Party Products are not covered by the warranty in <a href="#a967734">Section 6(a)</a>. For the avoidance of doubt,<strong>we make no representations or warranties with respect to any third-party product, including any (I) warranty of merchantability; (ii) warranty of fitness for a particular purpose; (iii) warranty of title; or (iv) warranty against infringement of intellectual property rights of a third party; whether express or implied by law, course of dealing, course of performance, usage of trade, or otherwise.</strong></li>
								<li>We shall not be liable for a breach of the warranties set forth in <a href="#a967734">Section 6(a)</a> and <a href="#a701571">Section 6(b)</a> unless: (i) you give written notice of the defective products or services, as the case may be, reasonably described, to us within five (5) business days of the time when you discover or ought to have discovered the defect; (ii) if applicable, we are given a reasonable opportunity after receiving the notice of breach of the warranty set forth in Section 6(a) to examine such products and you (if we so request) return such products to our place of business at your cost for the examination to take place there; and (iii) we reasonably verify your claim that the products or services are defective.</li>
								<li>We shall not be liable for a breach of the warranty set forth in <a href="#a967734">Section 6(a)</a> or <a href="#a701571">Section 6(b)</a> if: (i) you make any further use of such products after you give such notice; (ii) the defect arises because you failed to follow our oral or written instructions as to the storage, installation, commissioning, use or maintenance of the products; or (iii) you alter or repair such products without our prior written consent.</li>
								<li>Subject to <a href="#a625877">Section 6(e)</a> and <a href="#a484724">Section 6(f)</a> above, with respect to any such products during the Warranty Period, we shall, in our sole discretion, either: (i) repair or replace such products (or the defective part) or (ii) credit or refund the amounts paid by you for such products provided that, if we so request, you shall, at your expense, return such products to us.</li>
								<li>Subject to <a href="#a625877">Section 6(e)</a> and <a href="#a484724">Section 6(f)</a> above, with respect to any services subject to a claim under the warranty set forth in <a href="#a701571">Section 6(b)</a>, we shall, in our sole discretion, (i) repair or re-perform the applicable services or (ii) credit or refund the amounts paid by you for such services.</li>
								<li><strong>the remedies set forth in </strong><a href="#a935821"><strong>section 6(g)</strong></a><strong> and </strong><a href="#a272277"><strong>section 6(h)</strong></a><strong> shall be the your sole and exclusive remedy and our entire liability for any breach of the limited warranties set forth in </strong><a href="#a967734"><strong>section 6(a)</strong></a><strong> and </strong><a href="#a701571"><strong>section 6(b)</strong></a><strong>.</strong></li>
							</ul>
						</li>
						<li><u><span style="color: #003366;">Limitation of Liability</span></u>.
							<ul>
								<li><strong>in no event shall we be liable to you or any third party for any loss of use, revenue or profit or loss of data or diminution in value, or for any consequential, indirect, incidental, special, exemplary, or punitive damages </strong><strong>whether arising out of breach of contract, tort (including negligence) or otherwise, regardless of whether such damages were foreseeable and whether or not we have been advised of the possibility of such damages, and notwithstanding the failure of any agreed or other remedy of its essential purpose.</strong></li>
								<li><strong>in no event shall our aggregate liability arising out of or related to this agreement, whether arising out of or related to breach of contract, tort (including negligence) or otherwise, exceed the amounts paid by you for the products and services sold through the site.</strong></li>
							</ul>
						</li>
						<li><u><span style="color: #003366;">Compliance with Laws</span></u>. Buyer shall comply with all applicable laws, regulations and ordinances. Buyer shall maintain in effect all the licenses, permissions, authorizations, consents and permits that it needs to carry out its obligations under these Terms.&nbsp; Buyer shall comply with all export and import laws of all countries involved in the sale of the Goods under these Terms or any resale of the Goods by Buyer. Buyer assumes all responsibility for shipments of Goods requiring any government import clearance.</li>
						<li><u><span style="color: #003366;">Intellectual Property Use and Ownership</span></u>. You acknowledge and agree that:
							<ul>
								<li>All uses on this Site of the terms “sell,” “sale,” “resell,” “resale,” “purchase,” “price,” and the like mean the purchase or sale of a license. Each product and service marketed on this Site is made available solely for license, not sale, to you and other prospective customers under the terms, conditions and restrictions of the license agreement posted with the display or description of that specific product or service.</li>
								<li>You will comply with all terms and conditions of the specific license agreement for any product or service you obtain through this Site, including, but not limited to, all confidentiality obligations and restrictions on resale, use, reverse engineering, copying, making, modifying, improving, sublicensing, and transfer of those licensed products and services.</li>
								<li>You will not cause, induce or permit others’ noncompliance with the terms and conditions of any of these product and service license agreements.</li>
								<li>Megladon Manufacturing Group, Ltd. and its licensor(s) are and will remain the sole and exclusive owners of all intellectual property rights in and to each product and service made available on this Site and any related specifications, instructions, documentation or other materials, including, but not limited to, all related copyrights, patents, trademarks and other intellectual property rights, subject only to the limited license granted under the product’s or service’s license agreement. You do not and will not have or acquire any ownership of these intellectual property rights in or to the products or services made available through this Site, or of any intellectual property rights relating to those products or services.</li>
								<li>To the extent the Goods are to be manufactured in accordance with a Specification supplied by the Buyer, the Buyer shall indemnify the Seller against all liabilities, costs, expenses, damages and losses (including any direct, indirect or consequential losses, loss of profit, loss of reputation and all interest, penalties and legal and other reasonable professional costs and expenses) suffered or incurred by the Seller in connection with any claim made against Seller for actual or infringement of a third party’s intellectual property rights arising out of or in connection with the Seller’s use of the Specification. For purposes of this Section 29, “Specification” shall mean any specification for the Goods, including any related plans and drawings, that is supplied by the Buyer.&nbsp; The Seller reserves the right to amend the specification if required by an applicable statutory or regulatory requirements.</li>
							</ul>
						</li>
						<li><u><span style="color: #003366;">Privacy</span></u>. We respect your privacy and are committed to protecting it. Our Privacy Policy governs the processing of all personal data collected from you in connection with your purchase of products or services through the Site.</li>
						<li><u><span style="color: #003366;">Force Majeure</span></u>. We will not be liable or responsible to you, nor be deemed to have defaulted or breached these Terms, for any failure or delay in our performance under these Terms when and to the extent such failure or delay is caused by or results from acts or circumstances beyond our reasonable control, including, without limitation, acts of God, flood, fire, earthquake, explosion, governmental actions, war, invasion or hostilities (whether war is declared or not), terrorist threats or acts, riot or other civil unrest, national emergency, revolution, insurrection, epidemic, lockouts, strikes or other labor disputes (whether or not relating to our workforce), or restraints or delays affecting carriers or inability or delay in obtaining supplies of adequate or suitable materials, materials or telecommunication breakdown or power outage.</li>
						<li><u><span style="color: #003366;">Governing Law and Jurisdiction</span></u>. All matters arising out of or relating to these Terms are governed by and construed in accordance with the internal laws of the State of Texas without giving effect to any choice or conflict of law provision or rule (whether of the State of Texas or any other jurisdiction) that would cause the application of the laws of any jurisdiction other than those of the State of</li>
						<li><u><span style="color: #003366;">Waiver of Jury Trials and Binding Arbitration</span></u>.
							<ul>
								<li><strong>you and megladon manufacturing group, ltd. Are agreeing to give up any rights to litigate claims in a court or before a jury. Other rights that you would have if you went to court may also be unavailable or may be limited in arbitration.</strong></li>
							</ul>
						</li>
					</ol>
					<p><strong>any claim, dispute or controversy (whether in contract, tort or otherwise, whether pre-existing, present or future, and including statutory, consumer protection, common law, intentional tort, injunctive and equitable claims) between you and us arising from or relating in any way to your purchase of products or services through the site, will be resolved exclusively and finally by binding arbitration.</strong></p>
					<ul>
						<li>The arbitration will be administered by the American Arbitration Association (“<strong>AAA</strong>“) under its Commercial Arbitration Rules and Mediation Procedures (“<strong>Commercial Rules</strong>“). The arbitrator will have exclusive authority to resolve any dispute relating to arbitrability and/or enforceability of this arbitration provision, including any unconscionability challenge or any other challenge that the arbitration provision or the agreement is void, voidable or otherwise invalid. The arbitrator will be empowered to grant whatever relief would be available in court under law or in equity. Any award of the arbitrator(s) will be final and binding on each of the parties, and may be entered as a judgment in any court of competent jurisdiction.</li>
					</ul>
					<p>If any provision of this arbitration agreement is found unenforceable, the unenforceable provision will be severed and the remaining arbitration terms will be enforced.</p>
					<ol start="14">
						<li><u><span style="color: #003366;">Assignment</span></u>. You will not assign any of your rights or delegate any of your obligations under these Terms without our prior written consent. Any purported assignment or delegation in violation of this Section 14 is null and void. No assignment or delegation relieves you of any of your obligations under these Terms.</li>
						<li><u><span style="color: #003366;">No Waivers</span></u>. The failure by us to enforce any right or provision of these Terms will not constitute a waiver of future enforcement of that right or provision. The waiver of any right or provision will be effective only if in writing and signed by a duly authorized representative of Megladon Manufacturing Group, Ltd.</li>
						<li><u><span style="color: #003366;">No Third-Party Beneficiaries</span></u>. These Terms do not and are not intended to confer any rights or remedies upon any person or entity other than you.</li>
						<li><u><span style="color: #003366;">Notices</span></u>.
							<ul>
								<li><u><span style="color: #003366;">To You</span></u>. We may provide any notice to you under these Terms by: (i) sending a message to the e-mail address you provide or (ii) posting to the Site. Notices sent by e-mail will be effective when we send the e-mail and notices we provide by posting will be effective upon posting. It is your responsibility to keep your e-mail address current.</li>
								<li><u><span style="color: #003366;">To Us</span></u>. To give us notice under these Terms, you must contact us as follows: (i) by facsimile transmission to 512-583-0848; or (ii) by personal delivery, overnight courier or registered or certified mail to Megladon Manufacturing Group, Ltd, 12317 Technology Blvd., Suite 100, Austin, TX We may update the facsimile number or address for notices to us by posting a notice on the Site. Notices provided by personal delivery will be effective immediately. Notices provided by facsimile transmission or overnight courier will be effective one business day after they are sent. Notices provided by registered or certified mail will be effective three business days after they are sent.</li>
							</ul>
						</li>
						<li><u><span style="color: #003366;">Severability</span></u>. If any provision of these Terms is invalid, illegal, void or unenforceable, then that provision will be deemed severed from these Terms and will not affect the validity or enforceability of the remaining provisions of these Terms.</li>
						<li><u><span style="color: #003366;">Entire Agreement</span></u>. These Terms, our Website Terms of Use and our Privacy Policy will be deemed the final and integrated agreement between you and us on the matters contained in these Terms.</li>
					</ol>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
	( function(){
		$("#termAndConditionsId").click(function(){
			
			/* Term and conduction */

			$('.termAndConditions').modal('show');
			return true;

		});

	}());
</script>
