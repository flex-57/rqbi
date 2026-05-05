import type { Component } from 'vue'
import BlockText from './BlockText.vue'
import BlockImage from './BlockImage.vue'
import BlockSlider from './BlockSlider.vue'
import BlockVideo from './BlockVideo.vue'
import BlockCta from './BlockCta.vue'
import BlockDivider from './BlockDivider.vue'
import BlockStats from './BlockStats.vue'
import BlockCards from './BlockCards.vue'
import BlockTimeline from './BlockTimeline.vue'
import BlockFaq from './BlockFaq.vue'
import BlockGallery from './BlockGallery.vue'
import BlockMap from './BlockMap.vue'
import BlockForm from './BlockForm.vue'

const blockMap: Record<string, Component> = {
  text:         BlockText,
  image:        BlockImage,
  slider:       BlockSlider,
  video:        BlockVideo,
  cta:          BlockCta,
  divider:      BlockDivider,
  stats:        BlockStats,
  cards:        BlockCards,
  timeline:     BlockTimeline,
  faq:          BlockFaq,
  gallery:      BlockGallery,
  map:          BlockMap,
  form:         BlockForm,
}

export default blockMap
