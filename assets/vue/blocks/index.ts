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
import BlockContact from './BlockContact.vue'
import BlockFaq from './BlockFaq.vue'
import BlockGallery from './BlockGallery.vue'

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
  contact:      BlockContact,
  faq:          BlockFaq,
  gallery:      BlockGallery,
}

export default blockMap
