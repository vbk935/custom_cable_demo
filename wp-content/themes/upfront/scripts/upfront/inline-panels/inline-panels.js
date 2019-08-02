define([
	'scripts/upfront/inline-panels/panels',
	'scripts/upfront/inline-panels/panel',
	'scripts/upfront/inline-panels/item',
	'scripts/upfront/inline-panels/item-multi',
	'scripts/upfront/inline-panels/control',
	'scripts/upfront/inline-panels/multi-control',
	'scripts/upfront/inline-panels/tooltip-control',
	'scripts/upfront/inline-panels/sub-control',
	'scripts/upfront/inline-panels/padding-control',
	'scripts/upfront/inline-panels/control-panel',
	'scripts/upfront/inline-panels/dialog-control',
	'scripts/upfront/inline-panels/image-control',
	'scripts/upfront/inline-panels/collapsed-multi-control',
	'scripts/upfront/inline-panels/controls/visit-link',
	'scripts/upfront/inline-panels/controls/link-panel',
	'scripts/upfront/inline-panels/link-control',
	'scripts/upfront/inline-panels/controls/group-link-panel'
], function (Panels, Panel, Item, ItemMulti, Control, MultiControl, TooltipControl, SubControl, PaddingControl,
	ControlPanel, DialogControl, ImageEditControl, CollapsedMultiControl, VisitLinkControl, LinkPanelControl, LinkControl, GroupLinkPanelControl) {

	return {
		Panels: Panels,
		Panel: Panel,
		Item: Item,
		ItemMulti: ItemMulti,
		Control: Control,
		MultiControl: MultiControl,
		TooltipControl: TooltipControl,
		SubControl: SubControl,
		PaddingControl: PaddingControl,
		ControlPanel: ControlPanel,
		DialogControl: DialogControl,
		ImageControl: ImageEditControl,
		LinkControl: LinkControl,
		CollapsedMultiControl: CollapsedMultiControl,
		Controls: {
			VisitLink: VisitLinkControl,
			LinkPanel: LinkPanelControl,
			GroupLinkPanel: GroupLinkPanelControl
		}
	};
});
