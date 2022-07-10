<script>
    import icons from "../lib/icons.json";

    console.log(icons);
</script>

{#each Object.entries(icons) as [key, value], index}
    <img src="/{key}.svg" alt="{index}" />
{/each}