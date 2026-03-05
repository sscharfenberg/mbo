import type { Ref } from "vue";
import { ref, onMounted, onUnmounted } from "vue";

/** Return type of the {@link useStickyNav} composable. */
export type UseStickyNavReturn = {
    sentinel: Ref<HTMLElement | null>;
    isStuck: Ref<boolean>;
    activeSection: Ref<string | null>;
};

/**
 * Composable that drives a sticky nav's stuck state and active section
 * tracking using two separate `IntersectionObserver` instances.
 *
 * @param sections - The IDs of the sections the nav links point to. Must
 *   match the `id` attributes on the corresponding section elements.
 *
 * Returns a `sentinel` ref that must be bound to a zero-height element placed
 * immediately before the `<nav>` in the DOM — this is the anchor used to
 * detect when the nav has become stuck.
 *
 * `isStuck` and `activeSection` are reactive and update automatically as the
 * user scrolls or clicks a jump link. Both observers are disconnected when the
 * component that calls this composable is unmounted.
 */
export const useStickyNav = (sections: string[]): UseStickyNavReturn => {
    const sentinel = ref<HTMLElement | null>(null);
    const isStuck = ref(false);
    const activeSection = ref<string | null>(null);

    onMounted(() => {
        // The nav uses `position: sticky`, but CSS alone can't tell us when it's
        // actually stuck. The workaround: observe a zero-height sentinel placed
        // just before the nav. When the sentinel scrolls out of view (stops
        // intersecting), the nav must be stuck — so we flip `isStuck` to apply
        // the sticky variant styles.
        // threshold: [1] means the callback fires only when the sentinel is fully
        // visible or fully gone, avoiding intermediate states.
        const stickyObserver = new IntersectionObserver(
            entries => {
                isStuck.value = !entries[0]?.isIntersecting;
            },
            { threshold: [1] }
        );
        if (sentinel.value) stickyObserver.observe(sentinel.value);

        // rootMargin shrinks the effective viewport by 70% from the bottom,
        // creating a detection band in the top 30%. A section becomes "active"
        // the moment its top edge crosses into that band — whether the user
        // scrolled there or clicked a jump link.
        // threshold: 0 fires as soon as even 1px of the section enters the band.
        const sectionObserver = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        activeSection.value = entry.target.id;
                    }
                });
            },
            { rootMargin: "0px 0px -70% 0px", threshold: 0 }
        );
        sections.forEach(id => {
            const el = document.getElementById(id);
            if (el) sectionObserver.observe(el);
        });

        onUnmounted(() => {
            stickyObserver.disconnect();
            sectionObserver.disconnect();
        });
    });

    return { sentinel, isStuck, activeSection };
};